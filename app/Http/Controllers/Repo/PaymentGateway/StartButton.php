<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Http;

class StartButton extends Controller
{
    use StoreTransaction;

    const BASE_URL = "https://api.startbutton.tech"; // production url
    // const BASE_URL = "https://api.startbutton.builditdigital.co"; // sandbox
    // const STATUS_URL = "https://api.startbutton.builditdigital.co/transaction/status/";

    public function checkout($input, $mid_details)
    {
        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');
        $payload = [
            "email" => $input["email"],
            "amount" => $input["converted_amount"],
            "currency" => $input["converted_currency"],
            "reference" => $input["session_id"],
            "redirectUrl" => route("startbutton.callback", [$input["session_id"]])
        ];

        $response = Http::withHeaders(["Authorization" => "Bearer " . $mid_details->api_key, "Content-type" => "application/json"])->post(self::BASE_URL . "/transaction/initialize", $payload)->json();

        $this->storeMidPayload($input["session_id"], json_encode($payload));
        $input["gateway_id"] = "1";
        $this->updateGatewayResponseData($input, $response);

        // Log::info(["payload" => $payload, "startbutton-res" => $response]);

        if ($response == null || empty($response)) {
            return [
                "status" => "0",
                "reason" => "We are facing temporary issue from the bank side. Please contact us for more detail."
            ];
        } else if (isset($response["success"]) && $response["success"] == true) {
            return [
                'status' => '7',
                'reason' => '3DS link generated successful, please redirect.',
                'redirect_3ds_url' => $response["data"]
            ];
        } else {
            return [
                "status" => "0",
                "reason" => "Transaction could not processed!"
            ];
        }
    }

    public function callback(Request $request, $id)
    {
        $transaction = DB::table("transaction_session")->select("id", "payment_gateway_id", "request_data")->where("transaction_id", $id)->first();

        if ($transaction == null) {
            abort(404);
        }
        $input = json_decode($transaction->request_data, true);
        $mid = checkAssignMID($transaction->payment_gateway_id);
        $response = $this->statusApi($mid, $id);

        if ($response == null || empty($response) || $response["success"] == false) {
            $input["status"] = "0";
            $input["reason"] = "Transaction could not processed.";
        } else if (isset($response["data"]["transaction"]["status"]) && $response["data"]["transaction"]["status"] == "processing") {
            $input["status"] = "2";
            $input["reason"] = "Transaction is under process.please check after sometime.";
        } else if (isset($response["data"]["transaction"]["status"]) && $response["data"]["transaction"]["status"] == "successful") {
            $input["status"] = "1";
            $input["reason"] = "Transaction processed successfully!";
        } else if (isset($response["data"]["transaction"]["status"]) && $response["data"]["transaction"]["status"] == "initiated") {
            $input["status"] = "0";
            $input["reason"] = "Transaction could not processed!";
        }

        $input["gateway_id"] = $response["data"]["transaction"]["_id"] ?? "1";
        $this->updateGatewayResponseData($input, $response);
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);

    }

    public function webhook(Request $request)
    {
        $response = $request->all();

        if (isset($response["data"]["transaction"]["userTransactionReference"]) && $response["data"]["transaction"]["userTransactionReference"] != "") {

            $transaction = DB::table("transaction_session")->select("id", "request_data")->where("transaction_id", $response["data"]["transaction"]["userTransactionReference"])->first();

            if ($transaction == null) {
                exit();
            }

            $input = json_decode($transaction->request_data, true);

            if (isset($response["data"]["transaction"]["status"]) && $response["data"]["transaction"]["status"] == "processing") {
                $input["status"] = "2";
                $input["reason"] = "Transaction is under process.please check after sometime.";
            } else if (isset($response["data"]["transaction"]["status"]) && $response["data"]["transaction"]["status"] == "successful") {
                $input["status"] = "1";
                $input["reason"] = "Transaction processed successfully!";
            } else if (isset($response["data"]["transaction"]["status"]) && $response["data"]["transaction"]["status"] == "initiated") {
                $input["status"] = "0";
                $input["reason"] = "Transaction could not processed!";
            }

            $this->storeMidWebhook($input["session_id"], json_encode($response));
            $this->storeTransaction($input);
            exit();
        }

    }

    // * Status API 
    public function statusApi($mid, $id)
    {
        $url = self::BASE_URL . "/transaction/status/" . $id;
        $response = Http::withHeaders(["Content-type" => "application/json", "Authorization" => "Bearer " . $mid->secret_key])->get($url)->json();
        return $response;
    }
}