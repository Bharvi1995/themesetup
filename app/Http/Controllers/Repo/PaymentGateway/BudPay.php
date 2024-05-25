<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Http;
use Log;

class BudPay extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://api.budpay.com/api/s2s';

    // ================================================
    /* method : transaction
     * @param  : 
     * @Description : wonderland api call
     */// ==============================================
    public function checkout($input, $mid)
    {
        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');
        $dataCardPayload["data"] = [
            "number" => $input["card_no"],
            "expiryMonth" => $input['ccExpiryMonth'],
            "expiryYear" => substr($input["ccExpiryYear"], -2),
            'cvv' => $input['cvvNumber']
        ];
        $dataCardPayload["reference"] = $input["order_id"];

        $cardEncryptRes = Http::withHeaders(["Content-Type" => "application/json", "Authorization" => "Bearer " . $mid->secret_key])->post(self::BASE_URL . "/test/encryption", $dataCardPayload)->body();

        $myEncrypCard = $this->encryptData($dataCardPayload, $mid->public_key, $dataCardPayload["reference"]);

        Log::info(["budpay-encrypt-res" => $cardEncryptRes, "my_encrpted" => $myEncrypCard]);

        $data = [
            "email" => $input["email"],
            "amount" => strval($input['converted_amount']),
            "currency" => $input["converted_currency"],
            'callback' => route('budpay.callback', $input['session_id']),
            // 'callback' => "https://webhook.site/157e06c7-afb4-4094-8874-b78607450a77",
            "reference" => $input["order_id"],
            "card" => $myEncrypCard
        ];
        $response = Http::withHeaders(["Authorization" => "Bearer " . $mid->secret_key, "Content-Type" => "application/json"])->post(self::BASE_URL . "/transaction/initialize", $data)->json();

        $this->storeMidPayload($input["session_id"], json_encode($data));
        $input["gateway_id"] = "1";
        $this->updateGatewayResponseData($input, $response);

        Log::info(['budpay-response' => $response]);

        if ($response == null || empty($response)) {
            return [
                "status" => "0",
                "reason" => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                "order_id" => $input["order_id"]
            ];
        }

        if (isset($response["status"]) && $response["status"] == false) {
            return [
                "status" => "0",
                "reason" => $response["message"] ?? "Transaction could not processed.",
            ];

        } else if (isset($response["status"]) && $response["status"] == true && isset($response["alt"])) {
            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                'redirect_3ds_url' => $response['alt'],
            ];
        }

    }

    public function callback(Request $request, $id)
    {
        $request_data = $request->all();
        Log::info([
            'budpay_callback_data' => $request_data
        ]);
        $input_json = DB::table("transaction_session")
            ->select("request_data")
            ->where('transaction_id', $id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);

        if (isset($request_data['status']) && $request_data['status'] == 'success') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
        } elseif (isset($request_data['status']) && $request_data['status'] == 'failed') {
            $input['status'] = '0';
            $input['reason'] = 'Your transaction could not processed.';
        } else {
            $input['status'] = '2';
            $input['reason'] = 'Transaction is in pending.';
        }
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function webhook(Request $request)
    {
        $request_data = $request->all();
        Log::info([
            'budpay_webhook_data' => $request_data
        ]);
        $input_json = TransactionSession::where('order_id', $request_data["data"]["reference"])
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        if (isset($request_data["data"]['status']) && $request_data["data"]['status'] == 'success') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
            $this->storeTransaction($input);
        } elseif (isset($request_data["data"]['status']) && $request_data["data"]['status'] == 'failed') {
            $input['status'] = '0';
            $input['reason'] = 'Your transaction could not processed.';
            $this->storeTransaction($input);
        }

    }

    public function encryptData($data, $key, $reference_id)
    {
        // ksort($data);
        // $sorted = json_encode($data);
        // $signature = hash_hmac('sha512', $sorted, $key);
        // return $signature;
        Log::info(["encrypt_card" => $data, "key" => $key, "refrerence" => $reference_id]);
        $input = $data;
        $budpaykey = $key;
        $iv = substr($reference_id, 0, 16);
        return bin2hex(openssl_encrypt(json_encode($input), "aes-256-cbc", $budpaykey, OPENSSL_RAW_DATA, $iv));
    }
}
