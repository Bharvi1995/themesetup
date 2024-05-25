<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Jobs\YeloPayTxnJob;
use DB;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Http;

class YeloPay extends Controller
{

    use StoreTransaction;

    const BASE_URL = "https://api.yelopay.io/post";

    public function checkout($input, $midDetails)
    {
        $exponent = $this->currencyExponant($input['converted_currency']);
        if ($exponent == 3) {
            $input["converted_amount"] = number_format((float) $input['converted_amount'], 3, '.', '');
        } else {
            $input["converted_amount"] = number_format((float) $input['converted_amount'], 2, '.', '');
        }

        $hash = md5(strtoupper(strrev($input["email"]) . $midDetails->password . strrev(substr($input["card_no"], 0, 6) . substr($input["card_no"], -4))));

        $payload = [
            "action" => "SALE",
            "client_key" => $midDetails->merchant_key,
            "order_id" => $input["session_id"],
            "order_amount" => $input["converted_amount"],
            "order_currency" => $input["converted_currency"],
            "order_description" => "payment order " . $input["order_id"],
            "card_number" => $input["card_no"],
            "card_exp_month" => $input["ccExpiryMonth"],
            "card_exp_year" => $input["ccExpiryYear"],
            "card_cvv2" => $input["cvvNumber"],
            "payer_first_name" => $input["first_name"],
            "payer_last_name" => $input["last_name"],
            "payer_birth_date" => generateRandomDob(),
            "payer_address" => $input["address"],
            "payer_country" => $input["country"],
            "payer_state" => $input["state"],
            "payer_city" => $input["city"],
            "payer_zip" => $input["zip"],
            "payer_email" => $input["email"],
            "payer_phone" => $input["phone_no"],
            "payer_ip" => $input["ip_address"],
            // "term_url_3ds" => "https://webhook.site/14414f53-7cdd-4b27-b28a-fbc478f5485d",
            "term_url_3ds" => route('yelopay.return', [$input["session_id"]]),
            "callback_url" => route('yelopay.3ds.callback'),
            "hash" => $hash
        ];

        $response = Http::asForm()->post(self::BASE_URL, $payload)->json();
        // Log::info(["yelopay-payload" => $payload, "yelopay-res" => $response]);

        // * Store the mid payload
        $payload["card_number"] = cardMasking($payload["card_number"]);
        $payload["card_cvv2"] = "XXX";
        $this->storeMidPayload($input["session_id"], json_encode($payload));

        $input["gateway_id"] = isset($response["trans_id"]) && $response["trans_id"] != "" ? $response["trans_id"] : "1";

        $this->updateGatewayResponseData($input, $response);

        if (empty($response) || $response == null) {
            return [
                "status" => "0",
                "reason" => "We are facing temporary issue from the bank side. Please contact us for more detail."
            ];
        } else if (isset($response["status"]) && $response["status"] == "3DS" && $response["result"] == "REDIRECT") {
            return [
                'status' => '7',
                'reason' => '3DS link generated successful, please redirect.',
                'redirect_3ds_url' => route('yelopay.3ds', [$input["session_id"]])
            ];
        } else if (isset($response["status"]) && $response["status"] == "SETTLED" && $response["result"] == "SUCCESS") {
            return [
                "status" => "1",
                "reason" => "Transaction processed successfully!"
            ];
        } else if (isset($response["status"]) && $response["status"] == "PENDING" && $response["result"] == "SUCCESS") {
            return [
                "status" => "2",
                "reason" => "Your transaction is under process on bank side.please wait for sometime."
            ];
        } else if (isset($response["status"]) && $response["status"] == "DECLINED" && $response["result"] == "DECLINED") {
            return [
                "status" => "0",
                "reason" => isset($response["decline_reason"]) && $response["decline_reason"] != "" ? $response["decline_reason"] : "Your transansaction could not processed."
            ];
        } else {
            return [
                "status" => "0",
                "reason" => "Your transansaction could not processed."
            ];
        }

    }


    // * Redirect Form 
    public function redirectForm(Request $request, $id)
    {
        $transaction = DB::table('transaction_session')->select("request_data", "response_data", "payment_gateway_id")->where("transaction_id", $id)->first();

        if ($transaction == null) {
            abort(404);
        }

        $response = json_decode($transaction->response_data, true);
        $input = json_decode($transaction->request_data, true);


        if (isset($response["redirect_url"]) && !empty($response["redirect_params"]) && isset($response["redirect_method"])) {
            $data["url"] = $response["redirect_url"];
            $data["method"] = $response["redirect_method"];
            $data["params"] = $response["redirect_params"];
            return view('gateway.yelopay.index', $data);
        } else {
            $input["status"] = "0";
            $input["reason"] = "We are facing temporary issue from the bank side. Please contact us for more detail.";
            $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);

            return redirect($store_transaction_link);
        }

    }


    public function return (Request $request, $id)
    {
        $transaction = DB::table("transaction_session")->select("request_data", "response_data", "payment_gateway_id")->where("transaction_id", $id)->first();
        if ($transaction == null) {
            abort(404);
        }
        $input = json_decode($transaction->request_data, true);
        $statusRes = $this->statusAPI($transaction);

        if (empty($statusRes) || $statusRes == null || (isset($statusRes["status"]) && $statusRes["status"] == "ERROR")) {
            $input["status"] = "0";
            $input["reason"] = "Transaction could not processed!";
        } else if (isset($statusRes) && $statusRes["status"] == "DECLINED") {
            $input["status"] = "0";
            $input["reason"] = isset($statusRes["decline_reason"]) && $statusRes["decline_reason"] != "" ? $statusRes["decline_reason"] : "Transaction could not processed!";
        } else if (isset($statusRes) && $statusRes["status"] == "3DS" || $statusRes["status"] == "PENDING") {
            $input["status"] = "2";
            $input["reason"] = "Your transaction in pending state.please wait for sometime";
        } else if (isset($statusRes) && $statusRes["status"] == "SETTLED") {
            $input["status"] = "1";
            $input["reason"] = "Your transaction processed successfully!";
        } else {
            $input["status"] = "0";
            $input["reason"] = "Transaction could not processed!";
        }

        $this->updateGatewayResponseData($input, $statusRes);
        $this->storeTransaction($input);
        // convert response in query string

        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    // * Webhok notification
    public function callback(Request $request)
    {
        $response = $request->all();
        // Log::info(["yelowpay-webhook-res" => json_encode($response)]);
        return "OK";
        // $sessionId = isset($response["order_id"]) ? $response["order_id"] : null;
        // if ($sessionId) {
        //     $transaction = DB::table("transaction_session")->select("request_data")->where("transaction_id", $sessionId)->first();
        //     if ($transaction == null) {
        //         exit();
        //     }

        //     // * Update the webhook response
        //     $this->storeMidWebhook($sessionId, json_encode($response));

        //     $input = json_decode($transaction->request_data, true);

        //     if ((isset($response["status"]) && $response["status"] == "ERROR")) {
        //         $input["status"] = "0";
        //         $input["reason"] = "Transaction could not processed!";
        //     } else if (isset($response) && $response["status"] == "DECLINED") {
        //         $input["status"] = "0";
        //         $input["reason"] = isset($response["decline_reason"]) && $response["decline_reason"] != "" ? $response["decline_reason"] : "Transaction could not processed!";
        //     } else if (isset($response) && $response["status"] == "SETTLED") {
        //         $input["status"] = "1";
        //         $input["reason"] = "Your transaction processed successfully!";
        //     } else if (isset($response) && $response["status"] == "PENDING") {
        //         $input["status"] = "2";
        //         $input["reason"] = "Your transaction is under process. please wait for sometime.";
        //     } else if (isset($response) && $response["status"] == "3DS") {
        //         $input["status"] = "0";
        //         $input["reason"] = "User not completed the transaction process";
        //     }

        //     $this->storeTransaction($input);

        // }
    }

    // * Status API
    public function statusAPI($transaction)
    {

        $input = json_decode($transaction->request_data, true);
        $mid = checkAssignMID($transaction->payment_gateway_id);
        $hash = md5(strtoupper(strrev($input["email"]) . $mid->password . $input["gateway_id"] . strrev(substr($input["card_no"], 0, 6) . substr($input["card_no"], -4))));
        $payload = [
            "action" => "GET_TRANS_STATUS",
            "client_key" => $mid->merchant_key,
            "trans_id" => $input["gateway_id"],
            "hash" => $hash
        ];

        $statusRes = Http::asForm()->post(self::BASE_URL, $payload)->json();
        return $statusRes;
    }

    private function currencyExponant($code)
    {
        $currency_array = config('yeloPay');

        if (array_key_exists($code, $currency_array)) {
            return $currency_array[$code];
        } else {
            return 2;
        }
    }

    // * Update pending txn job
    public function pendingTxnJob(Request $request)
    {
        try {
            if ($request->get("password") != "f8d3h5883e8I7018608a0184f3445545e3e56489") {
                abort(404);
            }
            $mid = checkAssignMID("45");
            YeloPayTxnJob::dispatch($mid, self::BASE_URL);
            return response()->json(["status" => 200, "message" => "job added successfully!"]);
        } catch (\Exception $err) {
            return response()->json(["status" => 500, "message" => $err->getMessage()]);

        }
    }

}