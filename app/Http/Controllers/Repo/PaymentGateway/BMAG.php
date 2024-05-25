<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BMAG extends Controller
{

    use StoreTransaction;

    protected $transaction;

    const BASE_URL = "https://payment-api.bmag.in/api/BMAGPartner/upiqraddmoney";

    public function __construct()
    {
        $this->transaction = new Transaction();
    }

    public function checkout($input, $midDetails)
    {
        try {
            $input["converted_amount"] =  (int)ceil($input['converted_amount']);
            $payload = [
                "user_id" =>  (int)$midDetails->user_id,
                "api_key" => $midDetails->api_key,
                "amount" =>  (string)$input["converted_amount"],
                "user_email" => $input['email'],
                "user_mobile_number" => $input["phone_no"],
                "clientRefId" => $this->generateUniqueId(),
                "return_url" => route('bmag.redirect', [$input["order_id"]])
            ];

            $response = Http::withBasicAuth($midDetails->user_id, $midDetails->api_key)->withHeaders(["Content-type" => "application/json"])->post(self::BASE_URL, $payload)->json();



            Log::info(["bmag-payload" => $payload, "bmag-response" => $response]);

            if (empty($response)) {
                return [
                    'status' => '0',
                    'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                    'order_id' => $input['order_id'],
                ];
            } else if ($response["isSuccess"] == true && $response["status"] == 200) {
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successful, please redirect.',
                    'redirect_3ds_url' => $response['result']['url']
                ];
            } else if ($response["isSuccess"] == false && $response["status"] == 200) {
                return [
                    'status' => '0',
                    'reason' =>  isset($response["message"]) ? $response["message"] : "Your transaction declined by the bank.",
                    'order_id' => $input['order_id'],

                ];
            } else {
                return [
                    'status' => '0',
                    'reason' =>  "Your transaction could not processed.",
                    'order_id' => $input['order_id'],

                ];
            }
        } catch (\Exception $err) {
            Log::error(["Error is" => $err]);
            return [
                'status' => '0',
                'reason' => $err->getMessage(), // 'Your transaction could not processed.',
                'order_id' => $input['order_id']
            ];
        }
    }

    public function redirect(Request $request, $orderId)
    {
        $payload = $request->all();
        Log::info(["BMAG-callback-response" => $payload]);
        $transaction_session = DB::table('transaction_session')
            ->where('order_id', $orderId)
            ->first();
        if ($transaction_session == null) {
            return abort(404);
        }
        $input = json_decode($transaction_session->request_data, true);

        if ($payload["TransactionStatus"] == "1") {
            $input['status'] = '1';
            $input['reason'] = "Your transaction has been processed successfully.";
        } else if ($payload["TransactionStatus"] == "5") {
            $input['status'] = '0';
            $input['reason'] = isset($payload['Message']) ? $payload['Message'] : "Your transaction declined by the bank.";
        } else {
            return [
                'status' => '0',
                'reason' =>  "Your transaction could not processed.",
            ];
        }

        $input['gateway_id'] = isset($payload['TransactionId']) ? $payload['TransactionId'] : "1";
        $this->updateGatewayResponseData($input, $payload);
        $this->storeTransaction($input);

        // convert response in query string

        $store_transaction_link = $this->getRedirectLink($input);

        return redirect($store_transaction_link);
    }

    public function generateUniqueId()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $digits = '0123456789';

        $id = '';

        // Generate the first three uppercase letters
        for ($i = 0; $i < 3; $i++) {
            $id .= $characters[rand(0, strlen($characters) - 1)];
        }

        // Generate the next 16 digits
        for ($i = 0; $i < 16; $i++) {
            $id .= $digits[rand(0, strlen($digits) - 1)];
        }

        return $id;
    }
}
