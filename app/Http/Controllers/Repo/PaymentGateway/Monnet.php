<?php

namespace App\Http\Controllers\Repo\PaymentGateway;


use DB;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Http;

class Monnet extends Controller
{
    use StoreTransaction;

    const BASE_URL = "https://cert.monnetpayments.com/api-payin/v3/online-payments";
    const STATUS_URL = "https://cert.monnetpayments.com/ms-experience-payin/merchant";

    public function checkout($input, $midDetails)
    {
        return [
            'status' => '7',
            'reason' => '3DS link generated successful, please redirect.',
            'redirect_3ds_url' => route('monnet.payment.option.form', [$input['session_id'], encrypt($midDetails->country)])
        ];
    }


    // * Display payment option form
    public function paymentOptionForm(Request $request, $id, $country)
    {
        $decryptCountry = decrypt($country);
        $paymentOptions = config('monnet.' . $decryptCountry . "PaymentOption");
        $documentOptions = config('monnet.' . $decryptCountry . "DocumentsOptions");
        return view("gateway.monnet.selectPaymentType", compact('id', 'paymentOptions', 'documentOptions'));
    }

    // * Submit the payment option form
    public function paymentOptionFormSubmit(Request $request)
    {
        $payload = $request->validate([
            "session_id" => "required",
            "payinMethod" => "required",
            "documentType" => "required",
            "document" => "required"
        ]);

        $transaction = DB::table('transaction_session')->select("payment_gateway_id", "request_data")->where("transaction_id", $payload["session_id"])->first();

        if ($transaction == null) {
            Log::info(["monnet-transaction-session" => "transaction not found in payment Option Submit Form"]);
            abort(400);
        }
        $input = json_decode($transaction->request_data, true);
        $mid = checkAssignMID($transaction->payment_gateway_id);

        $input["converted_amount"] = $this->changeAmount($input["converted_amount"], $mid->country);
        $hash_data = $mid->merchant_id . $input['session_id'] . $input["converted_amount"] . $input["converted_currency"] . $mid->key;
        $signature = hash('sha512', $hash_data);
        //echo $signature
        $payload = [
            'payinMerchantID' => (int) $mid->merchant_id,
            'payinAmount' => $input["converted_amount"],
            'payinCurrency' => $input["converted_currency"],
            'payinMerchantOperationNumber' => $input['session_id'],
            'payinMethod' => $payload["payinMethod"],
            'payinVerification' => $signature,
            'payinTransactionOKURL' => Route('Monnet.redirect', [$input['session_id']]),
            // 'payinTransactionOKURL' => 'https://webhook.site/0447fd79-035a-4c63-b56b-a865c699998c',
            // 'payinTransactionErrorURL' => 'https://webhook.site/0447fd79-035a-4c63-b56b-a865c699998c',
            'payinTransactionErrorURL' => Route('Monnet.redirect', [$input['session_id']]),
            'payinExpirationTime' => '30',
            'payinLanguage' => 'EN',
            'payinCustomerEmail' => $input['email'],
            'payinCustomerName' => $input['first_name'],
            'payinCustomerLastName' => $input['last_name'],
            'payinCustomerTypeDocument' => $payload['documentType'],
            'payinCustomerDocument' => $payload['document'],
            'payinCustomerPhone' => $input['phone_no'],
            'payinCustomerAddress' => $input['address'],
            'payinCustomerCity' => $input['city'],
            'payinCustomerRegion' => $input['state'],
            'payinCustomerCountry' => $input['country'],
            'payinCustomerZipCode' => $input['zip'],
            'payinCustomerShippingName' => $input['first_name'] . ' ' . $input['last_name'],
            'payinCustomerShippingPhone' => $input['phone_no'],
            'payinCustomerShippingAddress' => $input['address'],
            'payinCustomerShippingCity' => $input['city'],
            'payinCustomerShippingRegion' => $input['state'],
            'payinCustomerShippingCountry' => $input['country'],
            'payinCustomerShippingZipCode' => $input['zip'],
            // 'payinRegularCustomer' => $input['first_name'].' '.$input['last_name'],
            // 'payinCustomerID' => $input['session_id'],
            // 'payinDiscountCoupon' => $input['session_id'],
            // 'payinFilterBy' => $input['session_id'],
            'payinProductID' => $input['session_id'],
            'payinProductDescription' => 'ECOM PAYMENT',
            'payinProductAmount' => $input["converted_amount"],
            'payinDateTime' => date('Y-m-d'),
            'payinProductSku' => 'ECOM PAYMENT',
            'payinProductQuantity' => '1',
            'URLMonnet' => 'https://cert.monnetpayments.com/api-payin/v3/online-payments',
            'typePost' => 'json',
        ];

        $response = Http::withHeaders(["Content-Type" => "application/json"])->post(self::BASE_URL, $payload)->json();

        // * Store request payload 
        $this->storeMidPayload($input["session_id"], json_encode($payload));

        // * Update the response
        $input["gateway_id"] = "1";
        $this->updateGatewayResponseData($input, $response);

        Log::info(["monnet-api-res" => $response]);
        if (empty($response) || $response == null) {
            $input["status"] = "0";
            $input["reason"] = "We are facing temporary issue from the bank side. Please contact us for more detail.";
            return $this->redirectToUserSite($input);
        } else if (isset($response["url"]) && $response["url"] != "") {
            return redirect($response["url"]);
        } else {
            $input["status"] = "0";
            $input["reason"] = "Your transaction could not processed.";
            return $this->redirectToUserSite($input);
        }
    }

    public function redirect(Request $request, $id)
    {
        $response = $request->all();
        Log::info(["monnet-return-res" => $response]);

        $transaction = DB::table('transaction_session')->select("request_data", "payment_gateway_id")->where("transaction_id", $id)->first();
        if ($transaction == null) {
            abort(404);
        }
        $input = json_decode($transaction->request_data, true);

        $mid = checkAssignMID($transaction->payment_gateway_id);
        $statusRes = $this->statusApi($mid, $id);

        $this->updateGatewayResponseData($input, $statusRes);

        if (empty($statusRes) || $statusRes == null) {
            $input["status"] = "0";
            $input["reason"] = "We are facing temporary issue from the bank side. Please contact us for more detail.";
        } else if (isset($statusRes["operations"][0]["payinStateID"]) && $statusRes["operations"][0]["payinStateID"] == "6") {
            $input["status"] = "0";
            $input["reason"] = "Your transaction could not processed.";
        } else if (isset($statusRes["operations"][0]["payinStateID"]) && $statusRes["operations"][0]["payinStateID"] == "5") {
            $input["status"] = "1";
            $input["reason"] = "Transaction processed successfully!";
        } else if (isset($statusRes["operations"][0]["payinStateID"]) && $statusRes["operations"][0]["payinStateID"] == "2") {
            $input["status"] = "2";
            $input["reason"] = "Your transaction is in pending state. please check after sometime!";
        } else {
            $input["status"] = "0";
            $input["reason"] = "Your transaction could not processed.";
        }

        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    // * listen webhook
    public function notify(Request $request)
    {

        $response = $request->all();
        Log::info(["monnet-webhook-res" => $response]);
        $sessionId = isset($response["payinMerchantOperationNumber"]) ? $response["payinMerchantOperationNumber"] : null;
        if (!$sessionId) {
            exit();
        }

        $this->storeMidWebhook($sessionId, json_encode($response));
        $transaction = DB::table("transaction_session")->select("request_data")->where("transaction_id", $sessionId)->first();
        if ($transaction == null) {
            exit();
        }


        $input = json_decode($transaction->request_data, true);

        if (isset($response["payinStateID"]) && $response["payinStateID"] == "5") {
            $input["status"] = "1";
            $input["reason"] = "Transaction processed successfully!";
        } else if (isset($response["payinStateID"]) && $response["payinStateID"] == "6") {
            $input["status"] = "0";
            $input["reason"] = "Your transaction could not processed.";
        } else if (isset($response["payinStateID"]) && $response["payinStateID"] == "2") {
            $input["status"] = "2";
            $input["reason"] = "Your transaction is in pending state. please check after sometime!";
        } else {
            $input["status"] = "0";
            $input["reason"] = "Your transaction could not processed.";
        }

        $this->storeTransaction($input);

    }

    public function statusApi($mid, $id)
    {
        $hash = hash("sha256", $mid->merchant_id . $mid->key);
        $response = Http::withHeaders(["authorization" => $hash])->post(self::STATUS_URL . "/" . $mid->merchant_id . "/operations", [
            "payinMerchantOperationNumber" => $id
        ])->json();

        return $response;
    }

    // * Redirect user to back 
    public function redirectToUserSite($input)
    {
        $store_transaction_link = $this->getRedirectLink($input);

        return redirect($store_transaction_link);
    }

    // * convert amount based on solution 
    public function changeAmount($amount, $country)
    {
        if ($country == "Chile" || $country == "Mexico") {
            $ceilAmt = ceil($amount);
            return number_format((float) $ceilAmt, 2, '.', '');
        } else {
            return number_format((float) $amount, 2, '.', '');
        }
    }
}