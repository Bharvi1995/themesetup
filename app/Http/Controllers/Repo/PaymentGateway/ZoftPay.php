<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZoftPay extends Controller
{
    use StoreTransaction;


    const BASE_URL = "https://trnxlog.com/api/v1.3/payment";




    public function checkout($input, $midDetails)
    {

        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');

        $payload = [
            "account_name" => $input["first_name"] . " " . $input["last_name"],
            "user_bank" => $this->getBankValue(),
            "account_no" => $input["phone_no"],
            "currency" => $input["converted_currency"],
            "amount" => $input["converted_amount"],
            "wid" => $input["session_id"],
            "mid" => $midDetails->mid,
            "apikey" => $midDetails->api_key,
            "browseragent" => request()->userAgent(),
            "M_SERVER_NAME" => request()->server("SERVER_NAME"),
            "M_HTTP_HOST" => request()->getHttpHost(),
            "server_ip" => config('custom.server_ip'),
            "payment_type" => "mbt",
            "transaction_type" => "1",
            "useragent" => request()->server("HTTP_USER_AGENT"),
            "ip" => $input["ip_address"],
            "phone" => $input["phone_no"],
            "email" => $input["email"],
            "no_redirect" => "1",
            "redirect_url" => route('zoftpay.callback', [$input["order_id"]]),
            "postback_url" => route('zoftpay.webhook', [$input["order_id"]])

        ];

        $response = Http::asForm()->post(self::BASE_URL, $payload)->json();

        Log::info(["zoftpay-response" => $response]);

        if (empty($response)) {
            return [
                'status' => '0',
                'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                'order_id' => $input['order_id'],
            ];
        } else if ($response["status"] == "PROCESSING" && isset($response["response"]["redirect_link"])) {
            return [
                'status' => '7',
                'reason' => '3DS link generated successful, please redirect.',
                'redirect_3ds_url' => $response["response"]["redirect_link"]
            ];
        } else if ($response["status"] == "FAILED") {
            return [
                'status' => '0',
                'reason' => isset($response['message']) ? $response['message'] : "Transaction got declined.",
                'order_id' => $input['order_id'],
            ];
        } else {
            return [
                'status' => '0',
                'reason' => isset($response['message']) ? $response['message'] : "Transaction got declined.",
                'order_id' => $input['order_id'],
            ];
        }
    }

    public function callback(Request $request, $orderId)
    {
        $payload = $request->all();
        Log::warning(["zoftpay-callback" => $payload]);

        $transaction_session = DB::table('transaction_session')
            ->where('order_id', $orderId)
            ->first();
        if ($transaction_session == null) {
            return abort(404);
        }
        $input = json_decode($transaction_session->request_data, true);
        $input["gateway_id"] = isset($payload["txid"]) ? $payload["txid"] : "1";

        if (isset($payload) && $payload["status"] == "FAILED") {
            $input['status'] = '0';
            $input['reason'] = isset($payload['message']) ? $payload["message"] : "Your transaction got declined";
        } else if (isset($payload) && $payload["status"] == "SUCCESS") {
            $input['status'] = '1';
            $input['reason'] = isset($payload['message']) ? $payload["message"] : "Transaction processed successfully!";
        } else {
            $input['status'] = '0';
            $input['reason'] = "Your transaction could not processed.";
        }

        $this->updateGatewayResponseData($input, $payload);
        $this->storeTransaction($input);

        // convert response in query string

        $store_transaction_link = $this->getRedirectLink($input);

        return redirect($store_transaction_link);
    }

    public function webhhok(Request $request, $orderId)
    {
        $payload = $request->all();
        Log::warning(["zoftpay-webhook-response" => $payload]);

        $transaction_session = DB::table('transaction_session')
            ->where('order_id', $orderId)
            ->first();
        if ($transaction_session == null) {
            return abort(404);
        }
        $input = json_decode($transaction_session->request_data, true);
        $input["gateway_id"] = isset($payload["txid"]) ? $payload["txid"] : "1";

        if (isset($payload) && $payload["status"] == "FAILED") {
            $input['status'] = '0';
            $input['reason'] = isset($payload['message']) ? $payload["message"] : "Your transaction got declined";
        } else if (isset($payload) && $payload["status"] == "SUCCESS") {
            $input['status'] = '1';
            $input['reason'] = isset($payload['message']) ? $payload["message"] : "Transaction processed successfully!";
        } else {
            $input['status'] = '0';
            $input['reason'] = "Your transaction could not processed.";
        }

        $this->updateGatewayResponseData($input, $payload);
        $this->storeTransaction($input);
    }

    // * Get random Bank Value

    public function getBankValue()
    {
        $banks = config('zoftpayBanks.banks');
        $randVal = rand(0, count($banks) - 1);
        return $banks[$randVal]["Value"];
    }
}