<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Jobs\DeclinedPayazaPendingTxnJob;
use App\Jobs\PayazaPendingTransactionsJob;
use DB;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Http;

class Payaza extends Controller
{

    use StoreTransaction;

    const BASE_URL = "https://cards-live.78financials.com/card_charge/";
    const STATUS_URL = "https://router-live.78financials.com/api/request/secure/payloadhandler";

    public function checkout($input, $midDetails)
    {

        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');
        $expiryYear = explode("20", $input["user_ccexpiry_year"]);
        $payload = [
            "service_type" => "Account",
            "service_payload" => [
                "request_application" => "Payaza",
                "application_module" => "USER_MODULE",
                "application_version" => "1.0.0",
                "request_class" => "UsdCardChargeRequest",
                "first_name" => $input["user_first_name"],
                "last_name" => $input["user_last_name"],
                "email_address" => $input["user_email"],
                "phone_number" => $input["user_phone_no"],
                "amount" => $input["converted_amount"],
                "transaction_reference" => $input["session_id"],
                "currency" => $input["converted_currency"],
                "description" => "A testpay Transaction " . $input["order_id"],
                "card" => [
                    "expiryMonth" => $input["user_ccexpiry_month"],
                    "expiryYear" => $expiryYear[1],
                    "securityCode" => $input["user_cvv_number"],
                    "cardNumber" => $input["user_card_no"],
                ],
                "callback_url" => route('payaza.return', [$input["session_id"]])
            ]
        ];
        $Key = "Payaza " . base64_encode($midDetails->api_key);
        $response = Http::withHeaders(['authorization' => $Key])->post(self::BASE_URL, $payload)->json();
        \Log::info(["payload" => $payload , "response" => $response]);
        $input["gateway_id"] = 1;
        $input["descriptor"] = isset($response["descriptor"]) ? $response["descriptor"] : "N/A";

        // * Unset the html data
        unset($response["threeDsHtml"]);
        $this->updateGatewayResponseData($input, $response);
        $payload["service_payload"]["card"]["cardNumber"] = cardMasking($payload["service_payload"]["card"]["cardNumber"]);
        $payload["service_payload"]["card"]["securityCode"] = "XXX";
        $this->storeMidPayload($input["session_id"], json_encode($payload));


        if (empty($response)) {
            return [
                'status' => '0',
                'reason' => "We are experiencing a temporary issue on the bank's end. Please reach out to us for further details and assistance.",
                'order_id' => $input['order_id'],
            ];
        } else if (isset($response["statusOk"]) && $response["statusOk"] == true && $response["do3dsAuth"] == true && isset($response["threeDsUrl"]) && $response["threeDsUrl"] != "") {
            return [
                'status' => '7',
                'reason' => "Please redirect to the specified 'payment_link' to complete the transaction processing.",
                'payment_link' => route('payaza.3ds', [$input["session_id"]])
            ];
        } else if (isset($response["statusOk"]) && $response["statusOk"] == true && $response["do3dsAuth"] == false && $response["paymentCompleted"] == true) {
            return [
                "status" => "1",
                "reason" => $response["debugMessage"] ?? "Your payment has been successfully completed."
            ];
        } else if (isset($response["statusOk"]) && $response["statusOk"] == false && $response["do3dsAuth"] == false && $response["paymentCompleted"] == false) {
            return [
                "status" => "0",
                "reason" => $response["debugMessage"] ?? "The transaction was unsuccessful."
            ];
        } else {
            return [
                'status' => '0',
                'reason' => $response['debugMessage'] ?? "The transaction was unsuccessful.",
                'order_id' => $input['order_id'],
            ];
        }
    }


    public function payazaReturn(Request $request, $id)
    {
        $response = $request->all();
        $payload = isset($response["payload"]) ? json_decode($response["payload"], true) : null;
        $transaction = DB::table('transaction_session')->select("id", "payment_gateway_id", "request_data")->where("transaction_id", $id)->first();
        if ($transaction == null || $payload == null) {
            exit();
        }
        $input = json_decode($transaction->request_data, true);

        // * Get the api_key and call the status API
        $mid = checkAssignMID($transaction->payment_gateway_id);
        $statusResponse = $this->statusAPI($mid, $id);
        if (!empty($statusResponse) && $statusResponse != null && $statusResponse["response_code"] == 200) {
            $payload["status"] = $statusResponse["response_content"]["transaction_status"];
        } else {
            $payload["status"] = "Failed";
        }
        if ($payload["statusOk"] == true && $payload["message"] == "Approved") {
            $input["status"] = "1";
            $input["reason"] = isset($payload["debugMessage"]) ? $payload["debugMessage"] : "Your payment has been successfully completed.";
        } else if ($payload["statusOk"] == false && $payload["status"] == "Failed") {
            $input["status"] = "0";
            $input["reason"] = isset($payload["debugMessage"]) ? $payload["debugMessage"] : "The transaction was unsuccessful.";
        } else if ($payload["status"] == "Pending") {
            $input["status"] = "2";
            $input["reason"] = isset($payload["debugMessage"]) ? $payload["debugMessage"] : "Your transaction is under process . Please wait for sometime!";
        } else {
            $input["status"] = "0";
            $input["reason"] = isset($payload["debugMessage"]) ? $payload["debugMessage"] : "The transaction was unsuccessful.";
        }

        $this->updateGatewayResponseData($input, $payload);
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);

    }

    // * Payaza 3DS url
    public function payaza3ds(Request $request, $id)
    {
        $transaction = DB::table("transaction_session")->select("id", "request_data", "payment_gateway_id", "response_data")->where("transaction_id", $id)->first();
        if ($transaction == null) {
            exit();
        }
        $responseData = json_decode($transaction->response_data, true);
        if (
            isset($responseData["threeDsUrl"]) && !empty($responseData["threeDsUrl"]) &&
            isset($responseData["formData"]) && !empty($responseData["formData"])
        ) {
            $actionUrl = $responseData["threeDsUrl"];
            $formData = $responseData["formData"];
            return view('gateway.payaza.index', compact('actionUrl', 'formData'));
        } else {
            abort(404);
        }
    }

    // * Payaza status API
    public function statusAPI($mid, $txnId)
    {

        $payload = [
            "service_type" => "Account",
            "service_payload" => [
                "request_application" => "Payaza",
                "application_module" => "USER_MODULE",
                "application_version" => "1.0.0",
                "request_class" => "CheckTransactionStatusRequest",
                "transaction_reference" => $txnId
            ]
        ];

        $Key = "Payaza " . base64_encode($mid->api_key);
        $response = Http::withHeaders(["Authorization" => $Key])->post(self::STATUS_URL, $payload)->json();
        $this->storeMidWebhook($txnId, json_encode($response));
        return $response;
    }

    // * Payaza cron job

    // public function payazaCron(Request $request)
    // {

    //     try {
    //         if ($request->get("password") != "b7323a883e7e4318608a0184f3445545e3e48043") {
    //             abort(404);
    //         }
    //         $mid = checkAssignMID("42"); // Test mid id => 38 && Live => 42
    //         PayazaPendingTransactionsJob::dispatch($mid, self::STATUS_URL);
    //         return response()->json(["status" => 200, "message" => "job added successfully!"]);
    //     } catch (\Exception $err) {
    //         return response()->json(["status" => 500, "message" => $err->getMessage()]);

    //     }
    // }

    // * Make old pending transaction as declined

    // public function declinedOldPendingTxn(Request $request)
    // {
    //     try {
    //         if ($request->get("password") != "b7323a883e7e4318608a0184f3445545e3e56412") {
    //             abort(404);
    //         }
    //         DeclinedPayazaPendingTxnJob::dispatch();
    //         return response()->json(["status" => 200, "message" => "job added successfully!"]);
    //     } catch (\Exception $err) {
    //         return response()->json(["status" => 500, "message" => $err->getMessage()]);

    //     }
    // }
}