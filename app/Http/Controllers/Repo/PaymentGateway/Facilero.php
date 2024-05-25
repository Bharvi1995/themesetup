<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Http;

class Facilero extends Controller
{
    use StoreTransaction;

    const BASE_URL = "https://preprod.facilero.com";

    public function checkout($input, $mid)
    {

        $cardPayload = [
            "card_no" => $input["card_no"],
            "ccExpiryYear" => $input["ccExpiryYear"],
            "cvvNumber" => $input["cvvNumber"],
            "ccExpiryMonth" => $input["ccExpiryMonth"],
        ];

        $encryptCardData = encrypt($cardPayload);
        DB::table("transaction_session")->where("transaction_id", $input["session_id"])->update(["extra_data" => $encryptCardData]);

        return [
            'status' => '7',
            'reason' => '3DS link generated successful, please redirect.',
            'redirect_3ds_url' => route('facilero.browser.info', [$input["session_id"]])
        ];

    }

    // * Browser info
    public function browserInfo($id)
    {

        return view('gateway.facilero.browser_info', compact('id'));

    }

    public function storeBrowserInfo(Request $request)
    {
        $body = $request->only(["session_id", "browser_info"]);
        $transaction = DB::table("transaction_session")->select("request_data", "extra_data", "payment_gateway_id")->where("transaction_id", $body["session_id"])->first();
        if ($transaction == null) {
            abort(404, "Url is not correct");
        }

        $mid = checkAssignMID($transaction->payment_gateway_id);
        $input = json_decode($transaction->request_data, true);
        $ccDetails = decrypt($transaction->extra_data);
        $browser_info = json_decode($body["browser_info"], true);
        $tokenRes = $this->generateAuthToken($mid);
        if ($tokenRes == null || empty($tokenRes)) {
            $input["status"] = "0";
            $input["reason"] = "We are facing temporary issue from the bank side. Please contact us for more detail.";
            $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        }

        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');
        $payload = [
            "authentication.memberId" => (int) $mid->merchant_id,
            // "authentication.accountId" => '',
            "authentication.checksum" => $this->generateMD5Checksum((int) $mid->merchant_id, $mid->secret_key, $input["session_id"], $input["converted_amount"]),
            "authentication.terminalId" => $input["card_type"] == "2" ? (int) $mid->visa_terminal : (int) $mid->master_terminal,
            "merchantTransactionId" => $input["session_id"],
            "amount" => $input['converted_amount'],
            "currency" => $input["converted_currency"],
            "orderDescriptor" => "testpay txn " . $input["order_id"],
            "shipping.country" => $input["country"],
            "shipping.city" => $input["city"],
            "shipping.state" => $input["state"],
            "shipping.postcode" => $input["zip"],
            "shipping.street1" => $input["address"],
            "customer.telnocc" => getPhoneCode($input["country"]),
            "customer.phone" => $input["phone_no"],
            "customer.email" => $input["email"],
            "customer.givenName" => $input["first_name"],
            "customer.surname" => $input["last_name"],
            "customer.ip" => $input["ip_address"],
            "customer.birthDate" => generateRandomDob("Ymd"),
            "card.number" => $ccDetails["card_no"],
            "card.expiryMonth" => $ccDetails["ccExpiryMonth"],
            "card.expiryYear" => $ccDetails["ccExpiryYear"],
            "card.cvv" => $ccDetails["cvvNumber"],
            "paymentBrand" => $input["card_type"] == "2" ? "VISA" : "MASTER CARD",
            "paymentMode" => "CC",
            "paymentType" => "DB",
            "merchantRedirectUrl" => route('facilero.redirect', ["id" => $input["session_id"]]),
            "notificationUrl" => route('facilero.webhook', ["id" => $input["session_id"]]),
            "tmpl_amount" => $input['converted_amount'],
            "tmpl_currency" => $input['converted_currency'],
            "customer.customerId" => $input["order_id"],
            // "attemptThreeD" => "Only3D",
            "deviceDetails.user_Agent" => $browser_info["user_Agent"],
            "deviceDetails.browserLanguage" => $browser_info["browserLanguage"],
            "deviceDetails.browserTimezoneOffset" => $browser_info["browserTimezoneOffset"],
            "deviceDetails.browserColorDepth" => $browser_info["browserColorDepth"],
            "deviceDetails.browserAcceptHeader" => $request->header('Accept'),
            "deviceDetails.browserScreenHeight" => $browser_info["browserScreenHeight"],
            "deviceDetails.browserScreenWidth" => $browser_info["browserScreenWidth"],
            "deviceDetails.browserJavaEnabled" => 'true',
        ];

        $response = Http::withHeaders(["AuthToken" => $tokenRes["AuthToken"]])->asForm()->post(self::BASE_URL . "/transactionServices/REST/v1/payments", $payload)->json();

        // * Store Payload in DB
        $payload["card.number"] = cardMasking($payload["card.number"]);
        $payload["card.cvv"] = "XXX";
        $this->storeMidPayload($input["session_id"], json_encode($payload));

        // * Remove encrypted card details from DB
        DB::table("transaction_session")->where("transaction_id", $input["session_id"])->update(["extra_data" => null]);

        $input["gateway_id"] = $response["paymentId"] ?? "1";
        $this->updateGatewayResponseData($input, $response);

        if (empty($response) || $response == null) {
            $input["status"] = "0";
            $input["reason"] = "We are facing temporary issue from the bank side. Please contact us for more detail.";
            $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        }
        if (isset($response['transactionStatus']) && $response['transactionStatus'] == "N") {
            $input["status"] = "0";
            $input["reason"] = $response['result']['description'] ?? "Transaction could not processed!";
        } else if (isset($response['transactionStatus']) && $response['transactionStatus'] == "3D") {
            return redirect()->route("facilero.redirect.form", [$input["session_id"]]);
        } else if (isset($response['transactionStatus']) && $response['transactionStatus'] == "P") {
            $input["status"] = "2";
            $input["reason"] = $response['result']['description'] ?? "Transaction is in pending state.please wait for sometime.";
        } else {
            $input["status"] = "2";
            $input["reason"] = $response['result']['description'] ?? "Transaction is in pending state.please wait for sometime.";
        }

        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    // * Redirect user back
    public function redirect(Request $request, $id)
    {
        $body = $request->all();
        $transaction = DB::table("transaction_session")->select("request_data", "payment_gateway_id")->where("transaction_id", $id)->first();
        if ($transaction == null) {
            abort(404, "Url is not correct");
        }
        $input = json_decode($transaction->request_data, true);
        if ($input["gateway_id"] == "1") {
            $input["gateway_id"] = $body["paymentId"];
        }
        $mid = checkAssignMID($transaction->payment_gateway_id);
        $statusRes = $this->statusApi($mid, $input["gateway_id"]);
        $this->updateGatewayResponseData($input, $statusRes);
        $input = $this->commonConditionCheck($statusRes, $input);
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);

    }

    // * Webhook 
    public function webhook(Request $request, $id)
    {
        $response = $request->all();
        $transaction = DB::table("transaction_session")->select("request_data")->where("transaction_id", $id)->first();
        if ($transaction == null) {
            exit();
        }

        $this->storeMidWebhook($id, json_encode($response));
        $input = json_decode($transaction->request_data, true);
        $input = $this->commonConditionCheck($response, $input);
        $this->storeTransaction($input);
        exit();

    }

    // * Common cstatus check condition
    public function commonConditionCheck($response, $input)
    {
        if (isset($response["status"]) && in_array($response["status"], ["authsuccessful", "capturesuccess", "settled", "payoutsuccessful"])) {
            $input["status"] = "1";
            $input["reason"] = "Transaction processed successfully!";
        } else if (isset($response["status"]) && in_array($response["status"], ["begun", "authstarted", "cancelstarted", "capturestarted"])) {
            $input["status"] = "2";
            $input["reason"] = "Transaction in pending state.please wait for sometime!";
        } else if (isset($response["status"]) && in_array($response["status"], ["authfailed", "capturefailed", "failed", "payoutfailed"])) {
            $input["status"] = "0";
            $input["reason"] = $response["remark"] ?? "Transaction could not processed!";
        } else if (isset($response["status"]) && in_array($response["status"], ["authcancelled", "cancelled"])) {
            $input["status"] = "0";
            $input["reason"] = $response["remark"] ?? "User cancelled the transaction process!";
        } else {
            $input["status"] = "0";
            $input["reason"] = $response["remark"] ?? "Transaction could not processed!";
        }

        return $input;
    }

    public function statusApi($mid, $gatewayId)
    {

        // * Generate checksum
        $values = $mid->merchant_id . "|" . $mid->secret_key . "|" . $gatewayId;
        $checksum = md5($values);
        $tokenRes = $this->generateAuthToken($mid);
        $statusPayload = [
            "authentication.memberId" => $mid->merchant_id,
            "authentication.checksum" => $checksum,
            "paymentType" => "IN",
            "idType" => "PID"
        ];

        $response = Http::withHeaders(["AuthToken" => $tokenRes["AuthToken"]])->asForm()->post(self::BASE_URL . "/transactionServices/REST/v1/payments/" . $gatewayId, $statusPayload)->json();
        return $response;

    }

    // * Redirect form
    public function redirectForm($id)
    {
        $transaction = DB::table("transaction_session")->select("request_data", "payment_gateway_id", "response_data")->where("transaction_id", $id)->first();
        if ($transaction == null) {
            abort(404, "Url is not correct");
        }

        $mid = checkAssignMID($transaction->payment_gateway_id);
        $response = json_decode($transaction->response_data, true);
        $actionUrl = $response['redirect']['url'];
        $fields = $response['redirect']['parameters'];
        $method = $response['redirect']['method'];
        return view('gateway.facilero.redirect_form', compact('actionUrl', 'fields', 'method'));

    }

    // * Generate auth token
    public function generateAuthToken($mid)
    {
        $payload = [
            "authentication.partnerId" => $mid->partner_id,
            "merchant.username" => $mid->username,
            "authentication.sKey" => $mid->secret_key
        ];
        $tokenRes = Http::asForm()->post("https://preprod.facilero.com/transactionServices/REST/v1/authToken", $payload)->json();
        return $tokenRes;

    }

    // * Generate checksum
    public function generateMD5Checksum($merchantId, $secret_key, $sessionId, $amount)
    {
        $values = $merchantId . "|" . $secret_key . "|" . $sessionId . "|" . $amount;
        $generatedCheckSum = md5($values);
        return $generatedCheckSum;
    }
}