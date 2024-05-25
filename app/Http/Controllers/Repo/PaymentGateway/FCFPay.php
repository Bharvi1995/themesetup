<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\Transaction;
use App\TransactionSession;

class FCFPay extends Controller
{

    // const BASE_URL = 'https://sandbox.fcfpay.com/api/v2'; // Test
    const BASE_URL = 'https://merchant.fcfpay.com/api/v2'; //Live

    use StoreTransaction;

    // ================================================
    /* method : __construct
        * @param  :
        * @Description : Create a new controller instance.
        */ // ==============================================
    public function __construct()
    {
        $this->transaction = new Transaction;
        $this->transactionSession = new TransactionSession;
    }

    public function checkout($input, $check_assign_mid)
    {
        $payload = [
            'domain' => config('app.url'),
            'order_id' => $input['order_id'],
            'user_id' => $input["session_id"],
            'amount' => $input["converted_amount"],
            'currency_name' => $input["converted_currency"],
            'order_date' => date("Y-m-d"),
            'redirect_url' => route("fcfpay.redirect", $input["session_id"])
        ];

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $check_assign_mid->api_key
        );

        $request_url = self::BASE_URL . '/create-order';
        $response_data = $this->curlPostRequest($request_url, $payload, $headers);
        \Log::info([
            'response' => $response_data,
        ]);
        if (isset($response_data) && $response_data->success) {
            $input['gateway_id'] = null;
            $this->updateGatewayResponseData($input, $response_data);
            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                'redirect_3ds_url' => $response_data->data->checkout_page_url,
            ];
        } else {
            return [
                'status' => '0',
                'reason' => 'Your transaction could not processed.',
                'order_id' => $input['order_id']
            ];
        }
        return $input;
    }

    public function redirect(Request $request, $session_id)
    {
        $request_data = $request->all();
        \Log::info([
            'fcfpay_redirect_data' => $request_data
        ]);
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        $data = ["order_id" => $input["order_id"]];
        $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $check_assign_mid->api_key
        );

        $request_url = self::BASE_URL . '/check-order';
        $response_data = $this->curlPostRequest($request_url, $data, $headers);
        if (isset($response_data) && isset($response_data->data->txs) && count($response_data->data->txs) > 0 && !empty($response_data->data->txs[0]->fiat_amount) && !empty($response_data->data->txs[0]->fiat_currency) && !empty($response_data->data->txs[0]->amount) && !empty($response_data->data->txs[0]->currency) && $response_data->data->txs[0]->deposited && $response_data->data->txs[0]->status == "deposited") {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
        } else {
            $input['status'] = '2';
            $input['reason'] = 'Transaction is in pending.';
        }
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function callback(Request $request)
    {
        $request_data = $request->all();
        \Log::info([
            'fcfpay_callback_data' => $request_data,
        ]);
        if (isset($request_data) && $request_data["data"]["type"] && $request_data["data"]["type"] == "deposit") {
            $input_json = TransactionSession::where('order_id', $request_data["data"]["order_id"])
                ->orderBy('id', 'desc')
                ->first();
            if ($input_json == null) {
                return abort(404);
            }
            $input = json_decode($input_json['request_data'], true);
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
            $transaction_response = $this->storeTransaction($input);
        }
        \Log::info('else');
        exit();
    }

    public function curlPostRequest($url, $data, $headers)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            $headers
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_SLASHES));
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        \Log::info([
            'curl-response' => $response,
        ]);
        return json_decode($response);
    }
}
