<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Mail;
use Session;
use Exception;
use Illuminate\Http\Request;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;


class NowPayments extends Controller
{
    use StoreTransaction;

    // const BASE_URL = 'https://api.sandbox.nowpayments.io/v1/'; // Test
    const BASE_URL = 'https://api.nowpayments.io/v1/'; // live
    // ================================================
    /* method : stripeForm
    * @param  :
    * @Description : Load stripe test form
    */// ==============================================
    public function checkout($input, $check_assign_mid)
    {
        // create payment method
        $payment_url = self::BASE_URL.'invoice';

        $payment_data = [
            "order_id" => $input['session_id'],
            "price_amount" => $input["converted_amount"],
            "price_currency" => $input["currency"],
            //"pay_currency" => "BTC",
            "ipn_callback_url" => route('nowpayments-crypto-callback', $input['session_id']),
            //"ipn_callback_url" => 'https://webhook.site/bcbfdb39-5e29-4e61-87f7-154778207404',
            "success_url" => route('nowpayments-cryptosuccess-callback', $input['session_id']),
            "cancel_url" => route('nowpayments-cryptocancel-callback', $input['session_id'])
        ];

        $request_headers = [
            'Content-Type: application/json',
            'x-api-key: '.$check_assign_mid->api_key
        ];
        $payload = json_encode($payment_data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $payment_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        $response_body = curl_exec($ch);

        curl_close ($ch);

        $response_data = json_decode($response_body, 1);
        //echo "<pre>";print_r($response_data);exit();
        \Log::info([
            'initialize' => $response_data
        ]);
        if(isset($response_data) && $response_data['id'] != '') {
            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                'redirect_3ds_url' => $response_data['invoice_url'],
            ];
        }
    }

    public function callback(Request $request, $session_id)
    {
        \Log::info([
            'NowPaymentsCrypto_CallBack' => $request->all()
        ]);
        $body = $request->all();
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $body['order_id'])
            ->first();
        if($data) {
            if ($body['payment_status'] == 'finished') {
                $input = json_decode($data->request_data, 1);
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccess successfully.';
                unset($input["api_key"]);
                $this->Transaction->storeData($input);
                \Log::info(['type' => 'webhook', 'body' => $body['order_id'].' confirm.']);
                exit();
            }
            else if ($body['payment_status'] == 'sending' || $body['payment_status'] == 'confirming') {
                $input['status'] = '0';
                $input['reason'] = 'Your transaction is pending.';
                unset($input["api_key"]);
                $this->Transaction->storeData($input);
                \Log::info(['type' => 'webhook', 'body' => $body['order_id'].' invalid.']);
                exit();
            }
            else {
                # transaction not confirm
                \Log::info(['type' => 'webhook', 'body' => $body['order_id'].' still not confirm.']);
                exit();
            }
        } else {
            \Log::info(['type' => 'webhook', 'body' => $body['order_id'].' still not confirm.']);
            exit();
        }
    }

    public function successCallback(Request $request, $session_id){
    	\Log::info([
            'NowPaymentsCrypto_success_CallBack' => $request->all()
        ]);
        $body = $request->all();
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $body['order_id'])
            ->first();
        if($data) {
            if ($body['payment_status'] == 'finished') {
                $input = json_decode($data->request_data, 1);
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccess successfully.';
                unset($input["api_key"]);
                $this->Transaction->storeData($input);
                \Log::info(['type' => 'webhook', 'body' => $body['order_id'].' confirm.']);
                exit();
            }
            else if ($body['payment_status'] == 'sending' || $body['payment_status'] == 'confirming') {
                $input['status'] = '0';
                $input['reason'] = 'Your transaction is pending.';
                unset($input["api_key"]);
                $this->Transaction->storeData($input);
                \Log::info(['type' => 'webhook', 'body' => $body['order_id'].' invalid.']);
                exit();
            }
            else {
                # transaction not confirm
                \Log::info(['type' => 'webhook', 'body' => $body['order_id'].' still not confirm.']);
                exit();
            }
        } else {
            \Log::info(['type' => 'webhook', 'body' => $body['order_id'].' still not confirm.']);
            exit();
        }
    }

    public function cancelCallback(Request $request, $session_id){
    	\Log::info([
            'NowPaymentsCrypto_cancel_CallBack' => $request->all()
        ]);
        $body = $request->all();
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $body['order_id'])
            ->first();
        if($data) {
            if ($body['payment_status'] == 'finished') {
                $input = json_decode($data->request_data, 1);
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccess successfully.';
                unset($input["api_key"]);
                $this->Transaction->storeData($input);
                \Log::info(['type' => 'webhook', 'body' => $body['order_id'].' confirm.']);
                exit();
            }
            else if ($body['payment_status'] == 'sending' || $body['payment_status'] == 'confirming') {
                $input['status'] = '0';
                $input['reason'] = 'Your transaction is pending.';
                unset($input["api_key"]);
                $this->Transaction->storeData($input);
                \Log::info(['type' => 'webhook', 'body' => $body['order_id'].' invalid.']);
                exit();
            }
            else {
                # transaction not confirm
                \Log::info(['type' => 'webhook', 'body' => $body['order_id'].' still not confirm.']);
                exit();
            }
        } else {
            \Log::info(['type' => 'webhook', 'body' => $body['order_id'].' still not confirm.']);
            exit();
        }
    }
}
