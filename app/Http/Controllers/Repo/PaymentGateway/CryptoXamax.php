<?php

namespace App\Http\Controllers\Repo\PaymentGateway;


use DB;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Http;

class CryptoXamax extends Controller
{
    use StoreTransaction;

    const BASE_URL = "https://api.sandbox.xamax.io/v1/"; // SANDBOX
    // const BASE_URL = "https://api.xamax.io/v1/"; // Production



    public function checkout($input, $midDetails)
    {
        $BtcToSatoshi = 100000000;

        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');
        $btcAmount = $this->getUSDToBTC($input['converted_amount']);
        $input["gateway_id"] = $this->generateRandomNumber();
        $accessToken = $this->generateAccessToken($midDetails->api_key);

        if ($btcAmount == null || $accessToken == null) {
            return [
                "status" => "0",
                "reason" => "There was some issue in bank API.please try again."
            ];
        }

        $amount = (int) ceil($btcAmount * $BtcToSatoshi);

        $payload = [
            "txId" => $input["gateway_id"],
            "code" => "btc",
            "amount" => strval($amount),
        ];

        $response = Http::withHeaders(["Authorization" => "Bearer " . $accessToken])->post(self::BASE_URL . "transaction/invoice", $payload)->json();

        $this->storeMidPayload($input["session_id"], json_encode($payload));
        $this->updateGatewayResponseData($input, $response);

        Log::info(["xamax-res" => $response]);

        if ($response == null || empty($response)) {
            return [
                "status" => "0",
                'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                'order_id' => $input['order_id'],
            ];
        } else if (isset($response["code"]) && $response["code"] == 3) {
            return [
                "status" => "0",
                'reason' => $response["message"] ?? "Transaction could not processed.",
            ];
        } else if (isset($response["walletAddress"]) && isset($response["amountRequiredUnit"])) {
            return [
                'status' => '7',
                'reason' => '3DS link generated successful, please redirect.',
                'redirect_3ds_url' => route('xamax.show.wallet', [$input["session_id"]])

            ];
        } else {
            return [
                "status" => "0",
                "reason" => "Transaction could not processed."
            ];
        }


    }

    // * Display wallet
    public function showWallet(Request $request, $id)
    {
        $transaction = DB::table("transaction_session")->select("id", "response_data", "request_data")->where("transaction_id", $id)->first();
        if ($transaction == null) {
            abort(404, "Url is not correct");
        }

        $response = json_decode($transaction->response_data, true);
        $input = json_decode($transaction->request_data, true);

        $input["status"] = "2";
        $input["reason"] = "Transaction is under process.please wait for sometime.";

        $this->storeTransaction($input);

        return view("gateway.xamax.wallet", compact("response", "id"));

    }

    // * Redirect user to their site
    public function userRedirect($id)
    {
        $transaction = DB::table("transaction_session")->select("id", "request_data")->where("transaction_id", $id)->first();
        if ($transaction == null) {
            abort(404, "Url is not correct");
        }
        $input = json_decode($transaction->request_data, true);
        $input["status"] = "2";
        $input["reason"] = "Transaction is under process.please wait for sometime.";
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    // * Callback for Xamax
    public function callback(Request $request)
    {
        $response = $request->all();
        Log::info(["xamax-webhook-res" => $response]);
        $transaction = DB::table("transaction_session")->select("id", "request_data", "gateway_id", "transaction_id")->where("gateway_id", $response["txId"])->first();
        if ($transaction == null) {
            exit();
        }

        // * Store MId Payload
        $this->storeMidWebhook($transaction->transaction_id, json_encode($response));
        $input = json_decode($transaction->request_data, true);

        if (isset($response["status"]) && $response["status"] == "transaction_status_pending") {
            $input["status"] = "2";
            $input["reason"] = "Transaction is under process.Please wait for sometime.";
        } else if (isset($response["status"]) && $response["status"] == "transaction_status_expired") {
            $input["status"] = "0";
            $input["reason"] = "Transaction got expired.";
        } else if (isset($response["status"]) && $response["status"] == "transaction_status_confirmed") {
            $input["status"] = "1";
            $input["reason"] = "Transaction processed successfully.";
        } else if (isset($response["status"]) && $response["status"] == "transaction_status_failed") {
            $input["status"] = "0";
            $input["reason"] = "Transaction could not processed.";
        } else if (isset($response["status"]) && $response["status"] == "transaction_status_canceled") {
            $input["status"] = "0";
            $input["reason"] = "User cancelled the transaction process.";
        }

        $this->storeTransaction($input);

        exit();
    }

    // * Status API
    public function statusAPI()
    {

    }

    public function getUSDToBTC($amount)
    {
        $key = config("custom.currency_converter_access_key");
        $response = Http::get('https://apilayer.net/api/live?access_key=' . $key . "&currencies=BTC&source=USD")->json();

        if (isset($response["quotes"]) && isset($response["quotes"]["USDBTC"])) {
            return $response["quotes"]["USDBTC"] * $amount;
        }
        return null;
    }

    // * Generate Access Token
    public function generateAccessToken($token)
    {
        $response = Http::asForm()
            ->post(self::BASE_URL . "auth/refresh", [
                'refresh_token' => $token,
            ])->json();

        // Log::info(["access-token-res" => $response]);
        if (isset($response["access_token"]) && $response["access_token"] != "") {
            return $response["access_token"];
        }

        return null;

    }

    // * Generate random number
    public function generateRandomNumber()
    {
        $min = 1000000000; // Minimum 10-digit number
        $max = 9999999999; // Maximum 10-digit number

        $randomNumber = mt_rand($min, $max);

        return $randomNumber;
    }

}

