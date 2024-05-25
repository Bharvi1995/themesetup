<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Http;

class Redfern extends Controller
{

    use StoreTransaction;

    const BASE_URL = "https://api.paymentsfolio.com/api/deposit/initiate";

    const STATUS_API = "https://api.paymentsfolio.com/api/deposit/status";

    public function checkout($input, $midDetails)
    {

        try {
            $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');
            $card_type = "VISA";
            if ($input["card_type"] == "3") {
                $card_type = "MASTERCARD";
            } else if ($input["card_type"] == "1") {
                $card_type = "AMEX";
            }

            $ccExpiry = explode("20", $input["ccExpiryYear"]);

            $payload = [
                "fname" => $input["first_name"],
                "lname" => $input["last_name"],
                "email" => $input["email"],
                "phone" => $this->cleanPhoneNumber($input["phone_no"]),
                "address" => $input["address"],
                "city" => $input["city"],
                "state" => $input["state"],
                "postalcode" => $input["zip"],
                "country" => $input["country"],
                "currency" => $input["converted_currency"],
                "amount" => $input["converted_amount"],
                "ccnumber" => $input["card_no"],
                "cctype" => $card_type,
                "ccname" => $input["first_name"] . " " . $input["last_name"],
                "ccexpmonth" => $input["ccExpiryMonth"],
                "ccexpyear" => $ccExpiry[1],
                "cvv" => $input["cvvNumber"],
                "merchantref" => $input["session_id"],
                "userip" => $input["ip_address"],
                "callbackurl" => route('redfern.callback', [$input["session_id"]]),
                "returnurl" => route('redfern.return', [$input["session_id"]])

            ];

            // * Generate Hash
            $hashStr = $payload["fname"] . $payload["lname"] . $payload["phone"] . $payload["email"] . $payload["merchantref"];
            $sha256 = hash("sha256", $hashStr . $midDetails->secret_key);
            $hash = hash_hmac("sha512", $sha256, $midDetails->key);

            $response = Http::withHeaders(["CODE" => $midDetails->key, "HASH" => $hash])->post(self::BASE_URL, $payload)->json();

            // Log::info(["redfern-payload" => $payload, "redfern-response" => $response]);

            // * Store mid payload in DB
            $payload["ccnumber"] = cardMasking($payload["ccnumber"]);
            $payload["cvv"] = "XXX";
            $this->storeMidPayload($input["session_id"], json_encode($payload));

            // * Get txn id and update it as gateway id 
            $input["gateway_id"] = isset($response["txnresponse"]["txn_id"]) ? $response["txnresponse"]["txn_id"] : "1";
            $this->updateGatewayResponseData($input, $response);

            if (empty($response) || $response == null) {
                return [
                    "status" => "0",
                    "reason" => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                ];
            } else if ($response["status"] == "success" && isset($response["txnresponse"]["txn_paymenturl"]) && $response["txnresponse"]["txn_paymenturl"] != "") {
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successful, please redirect.',
                    'redirect_3ds_url' => $response["txnresponse"]["txn_paymenturl"]
                ];
            } else if (isset($response["txnresponse"]["txn_status"]) && $response["txnresponse"]["txn_status"] == "APPROVED") {
                return [
                    "status" => "1",
                    "reason" => isset($response["txnresponse"]["txn_message"]) && $response["txnresponse"]["txn_message"] != "" ? $response["txnresponse"]["txn_message"] : "Transaction processed successfully!."
                ];
            } else {
                return [
                    "status" => "0",
                    "reason" => isset($response["txnresponse"]["txn_message"]) ? $response["txnresponse"]["txn_message"] : "Transaction could not processed."
                ];
            }

        } catch (\Exception $err) {
            Log::info(["redfern-checkout-catch-err" => $err->getMessage()]);
            return [
                "status" => "0",
                "reason" => "We are facing temporary issue from the bank side. Please contact us for more detail.",
            ];
        }

    }

    // * return callback
    public function return (Request $request, $id)
    {
        $responsePayload = $request->all();

        // Log::info(["redfern-return-res" => $responsePayload]);

        $transaction = DB::table("transaction_session")->select("payment_gateway_id", "request_data")->where("transaction_id", $id)->first();
        if ($transaction == null) {
            abort(404);
        }

        $input = json_decode($transaction->request_data, true);
        $mid = checkAssignMID($transaction->payment_gateway_id);
        $statusRes = $this->txnStatus($mid, $responsePayload["txn_id"]);

        if (empty($statusRes) || $statusRes == null) {
            $input["status"] = "0";
            $input["reason"] = "Transaction could not processed.please try again.";
        } else if ($statusRes["status"] == "success" && isset($statusRes["transaction"]["tran_status"]) && $statusRes["transaction"]["tran_status"] == "APPROVED") {
            $input["status"] = "1";
            $input["reason"] = "Transaction processed successfully!";
        } else if ($statusRes["status"] == "success" && isset($statusRes["transaction"]["tran_status"]) && $statusRes["transaction"]["tran_status"] == "DECLINED") {
            $input["status"] = "0";
            $input["reason"] = isset($statusRes["transaction"]["tran_message"]) && $statusRes["transaction"]["tran_message"] != "" ? $statusRes["transaction"]["tran_message"] : "Transaction could not processed";
        } else {
            $input["status"] = "0";
            $input["reason"] = isset($statusRes["transaction"]["tran_message"]) && $statusRes["transaction"]["tran_message"] != "" ? $statusRes["transaction"]["tran_message"] : "Transaction could not processed";
        }

        $input["gateway_id"] = $responsePayload["txn_id"] ?? "1";
        $this->updateGatewayResponseData($input, $statusRes);
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);

        return redirect($store_transaction_link);

    }

    // * Callback url
    public function callback(Request $request, $id)
    {
        $responsePayload = $request->all();

        Log::info(["redfern-webhook-res" => $responsePayload]);
    }

    public function txnStatus($mid, $txnId)
    {
        // * Generate Hash
        $sha256 = hash("sha256", $txnId . $mid->secret_key);
        $hash = hash_hmac("sha512", $sha256, $mid->key);

        $response = Http::withHeaders(["CODE" => $mid->key, "HASH" => $hash])->post(self::STATUS_API, ["txn_id" => $txnId])->json();

        return $response;
    }

    // * To clean the phone number
    function cleanPhoneNumber($phoneNumber)
    {
        // Remove white spaces and special characters except digits
        $cleanedNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        return $cleanedNumber;
    }
}