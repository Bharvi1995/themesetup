<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Http;

class KiwiPay extends Controller
{

    use StoreTransaction;

    const BASE_URL = "https://sandbox-createpo-idjxqqeiaa-uc.a.run.app"; // Sandbox URL
    // const BASE_URL = "https://prod-createpo-idjxqqeiaa-uc.a.run.app"; // production url
    const CREATE_TRANSACTION = "https://sandbox-sendpm-idjxqqeiaa-uc.a.run.app";

    const STATUS_API = "https://sandbox-retrievepo-idjxqqeiaa-uc.a.run.app";

    public function checkout($input, $midDetails)
    {

        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');
        $payload = [
            "apikey" => $midDetails->api_key,
            "commerceId" => $midDetails->customer_id,
            "merchantId" => $midDetails->merchant_id,
            "externalId" => $input["session_id"],
            "amount" => intval($input["converted_amount"] * 100),
            "currency" => $input["converted_currency"],
            "type" => "card",
            "disabledNetworks" => ["carnet"],
            "threeDS" => true,
            "prebuiltCheckoutSecuritySteps" => ["email"],
            "description" => "testpay Transaction " . $input["order_id"]
        ];

        $response = Http::withHeaders(["Content-Type" => "application/json", "Accept" => "application/json"])->post(self::BASE_URL, $payload)->json();

        // * Store mid payload
        $this->storeMidPayload($input["session_id"], json_encode($payload));
        $input["gateway_id"] = "1";
        // * update the response
        $this->updateGatewayResponseData($input, $response);

        if ($response == null || empty($response)) {
            return [
                'status' => '0',
                'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                'order_id' => $input['order_id'],
            ];
        } else if (isset($response["status"]) && $response["status"] == 200 && isset($response["id"])) {
            $secondPayload = [
                "id" => $response["id"],
                "apikey" => $midDetails->api_key,
                "commerceId" => $midDetails->customer_id,
                "merchantId" => $midDetails->merchant_id,
                "paymentMethod" => [
                    "type" => "card",
                    "card" => [
                        "number" => $input["card_no"],
                        "expMonth" => $input["ccExpiryMonth"],
                        "expYear" => $input["ccExpiryYear"],
                        "cvc" => $input["cvvNumber"]
                    ]
                ]
            ];

            $cardResponse = Http::withHeaders(["Content-Type" => "application/json", "Accept" => "application/json"])->post(self::CREATE_TRANSACTION, $secondPayload)->json();
            Log::info(["kiwipay-card-res" => $cardResponse]);

            // * Store payload in mid payload
            $secondPayload["paymentMethod"]["card"]["number"] = cardMasking($secondPayload["paymentMethod"]["card"]["number"]);
            $secondPayload["cvc"]["card"]["number"] = "XXX";
            $payload["cardPayload"] = $secondPayload;

            $input["gateway_id"] = $response["id"];

            $this->storeMidPayload($input["session_id"], json_encode($secondPayload));
            $this->updateGatewayResponseData($input, $cardResponse);

            if ($cardResponse == null || empty($cardResponse)) {
                return [
                    'status' => '0',
                    'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                    'order_id' => $input['order_id'],
                ];
            } else if (isset($cardResponse["status"]) && $cardResponse["status"] == 300 && isset($cardResponse["url"])) {
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successful, please redirect.',
                    'redirect_3ds_url' => $cardResponse["url"]
                ];
            } else if (isset($cardResponse["status"]) && $cardResponse["status"] == 200 && isset($cardResponse["data"]["status"]) && $cardResponse["data"]["status"] == "succeeded") {
                return [
                    "status" => "1",
                    "reason" => "Your transaction processed successfully!."
                ];
            } else {
                return [
                    "status" => "0",
                    "reason" => "Your transaction could not processed."
                ];
            }

        } else {
            return [
                "status" => "0",
                "reason" => "Your transaction could not processed."
            ];
        }
    }


    public function success(Request $request)
    {
        $response = $request->all();
        $transaction = DB::table("transaction_session")->select("request_data", "payment_gateway_id")->where("transaction_id", $response["externalId"])->first();
        if ($transaction == null) {
            abort(404);
        }

        $input = json_decode($transaction->request_data, true);
        $mid = checkAssignMID($transaction->payment_gateway_id);
        $statusRes = $this->getStatus($mid, $response["poID"]);
        if (empty($statusRes) || $statusRes == null) {
            abort(404);
        }

        if (isset($statusRes["data"]["status"]) && $statusRes["data"]["status"] == "success") {
            $input["status"] = "1";
            $input["reason"] = "Transaction processed successfully!";
        } else if (isset($statusRes["data"]["status"]) && $statusRes["data"]["status"] == "failed") {
            $input["status"] = "0";
            $input["reason"] = "Transaction could not processed.";
        } else if (isset($statusRes["data"]["status"]) && $statusRes["data"]["status"] == "pending") {
            $input["status"] = "2";
            $input["reason"] = "Your transaction is under process . Please wait for sometime!";
        } else {
            $input["status"] = "0";
            $input["reason"] = "Transaction could not processed.";
        }

        $this->updateGatewayResponseData($input, $statusRes);
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);

        return redirect($store_transaction_link);
    }

    public function error(Request $request)
    {
        $response = $request->all();
        Log::info(["kiwipay-error-callback" => $response]);
        $transaction = DB::table("transaction_session")->select("request_data")->where("transaction_id", $response["externalId"])->first();
        if ($transaction == null) {
            abort(404);
        }

        $input = json_decode($transaction->request_data, true);
        $input["status"] = "0";
        $input["reason"] = "Transaction could not processed.";

        $this->updateGatewayResponseData($input, $response);
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function webhook(Request $request)
    {
        $response = $request->all();
        if (isset($response["metadata"]["externalId"]) && $response["metadata"]["externalId"]) {
            $this->storeMidWebhook($response["metadata"]["externalId"], json_encode($response));

            $transaction = DB::table("transaction_session")->select("request_data")->where("transaction_id", $response["metadata"]["externalId"])->first();
            if ($transaction == null) {
                exit();
            }
            $input = json_decode($transaction->request_data, true);
            if (isset($response["status"]) && $response["status"] == "success") {
                $input["status"] = "1";
                $input["reason"] = "Transaction processed successfully!";
            } else if (isset($response["status"]) && $response["status"] == "failed") {
                $input["status"] = "0";
                $input["reason"] = "Transaction could not processed.";
            } else if (isset($response["status"]) && $response["status"] == "pending") {
                $input["status"] = "2";
                $input["reason"] = "Your transaction is under process . Please wait for sometime!";
            }
            $this->storeTransaction($input);

        } else {
            Log::info(["kiwipay-webhook-callback" => $response]);
        }

    }

    public function getStatus($mid, $id)
    {
        $payload = [
            "id" => $id,
            "apikey" => $mid->api_key,
            "commerceId" => $mid->customer_id,
            "merchantId" => $mid->merchant_id,
        ];

        $response = Http::withHeaders(["Content-Type" => "application/json"])->post(self::STATUS_API, $payload)->json();

        return $response;
    }
}