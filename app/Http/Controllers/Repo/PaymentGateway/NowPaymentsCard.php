<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Mail;
use Session;
use Exception;
use Illuminate\Http\Request;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;


class NowPaymentsCard extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://api.nowpayments.io/v1/invoice'; // Test
    //const BASE_URL = 'https://api.nowpayments.io/v1/'; // live
    // ================================================
    /* method : stripeForm
    * @param  :
    * @Description : Load stripe test form
    */// ==============================================
    public function checkout($input, $check_assign_mid)
    {
        // create payment method
        $payment_url = self::BASE_URL;
        $payment_data = [
            "order_id" => $input['order_id'],
            "price_amount" => $input["converted_amount"],
            "price_currency" => $input["converted_currency"],
            //"pay_currency" => $input["converted_currency"],
            "order_description" => $input['session_id'],
            "ipn_callback_url" => route('nowpayments-callback', $input['session_id']),
            "success_url" => route('nowpayments-success-callback', $input['session_id']),
            "cancel_url" => route('nowpayments-cancel-callback', $input['session_id'])
        ];
        
        $request_headers = [
            'Content-Type: application/json',
            'x-api-key: '.$check_assign_mid->api_key
        ];
        \Log::info([
            'nowpayments-input' => $payment_data
        ]);
        \Log::info([
            'nowpayments-header' => $request_headers
        ]);
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
            'NowPaymentsCards_CallBack' => $request->all()
        ]);
    }

    public function successCallback(Request $request, $session_id){
    	\Log::info([
            'NowPaymentsCards_success_CallBack' => $request->all()
        ]);
    }

    public function cancelCallback(Request $request, $session_id){
    	\Log::info([
            'NowPaymentsCards_cancel_CallBack' => $request->all()
        ]);
    }
}
