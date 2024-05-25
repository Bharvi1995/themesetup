<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Mail;
use Session;
use Exception;
use App\User;
use App\Transaction;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Cartalyst\Stripe\Laravel\Facades\Stripe;


class Texcenthpp extends Controller
{
    use StoreTransaction;

    
    public function checkout($input, $check_assign_mid)
    {
        $data = [
            "email" => $check_assign_mid->email,
            "password" => $check_assign_mid->password,
        ];

        $url = 'https://payments.cardpayz.com/api/login';
        
        $headers = array('Content-Type: application/json');
        try {
            $response_body = $this->curlPost($url, $data, $headers);
            $result = json_decode($response_body, true);
        } catch (\Exception $e) {
        }
        if ( isset($result['statusCode']) && $result['statusCode'] == 200 && isset($result['data']['user']['token']) && $result['data']['user']['token'] != null) {
            $token = $result['data']['user']['token'];
            $request_data = [
                'orderId' => $input['session_id'],
                'amount' => $input['converted_amount'],
                'currency' => $input['currency'],
                'payerEmail' => $input['email'],
                'payerName' => $input['first_name']. ' ' .$input['last_name'],
                // 'cardNo' => $input['card_no'],
                // 'expDate' => $input['ccExpiryMonth']. '-' .$input['ccExpiryYear'],
                // 'cvv2' => $input['cvvNumber'],
                // 'redirectUrl' => 'https://google.com/',
                // 'notifyUrl' => 'https://webhook.site/87bff569-b33e-4587-80b9-3180cbdc4475'
                'redirectUrl' => route('texCent.redirect', $input['session_id']),
                'notifyUrl' => route('texCent.notify', $input['session_id'])
            ];
            
            $request_url = 'https://payments.cardpayz.com/api/paymentHpp';
            $request_header = [
                'Content-Type: application/json',
                'Authorization: Bearer '.$token
            ];
            // request for payment
            $request_response = $this->curlPost($request_url, $request_data, $request_header);
            \Log::info([
                'texcentfx_request_response' => $request_response
            ]);
            try {
                $request_result = json_decode($request_response, true);
                $request_result['token'] = $token;
                if (isset($request_result['data']['payment']['transaction_id']) && $request_result['data']['payment']['transaction_id'] != null) {
                    $input['gateway_id'] = $request_result['data']['payment']['transaction_id'];
                    $this->updateGatewayResponseData($input, $request_result);
                }
            } catch (Exception $e) {
                $request_result = [];
            }
            if ( isset($request_result['statusCode']) && $request_result['statusCode'] == 200 && isset($request_result['data']['payment']['payment_url']) && $request_result['data']['payment']['payment_url'] != null) {
                // redirect to acquirer server
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect.',
                    'redirect_3ds_url' => $request_result['data']['payment']['payment_url'],
                ];

            }elseif ( isset($request_result['statusCode']) && $request_result['statusCode'] == 400 && isset($request_result['message']) && $request_result['message'] != null ) {
                $input['status'] = '0';
                $input['reason'] = $request_result['message'];
                return [
                    'status' => '0',
                    'reason' => $input['reason'],
                    'order_id' => $input['order_id'],
                ];
            }
        }
        $input['status'] = '0';
        $input['reason'] = 'Transaction authentication failed.';
        return [
            'status' => '0',
            'reason' => $input['reason'],
            'order_id' => $input['order_id'],
        ];
    }

    public function redirect(Request $request, $session_id)
    {
        $request_data = $request->all();
        \Log::info([
            'redirect_inputs' => $request_data
        ]);
        $input_json = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first();
        
        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json->request_data, true);
        $session_response = json_decode($input_json->response_data, true);
        $token = $session_response['token'];
        $status_url = 'https://payments.cardpayz.com/api/paymentStatusByOrderId?orderId='.$session_id;
        $headers = [
            'Authorization: Bearer '.$token
        ];
        try {
            $verify_json = $this->curlGet($status_url, $headers);
            $verify = json_decode($verify_json, true);
        } catch (\Exception $e) {
            $verify = null;
        }
        if ( isset($verify['statusCode']) && $verify['statusCode'] == 200 && isset($verify['data']['transaction']['status']) && $verify['data']['transaction']['status'] == 'successful') {

            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';
        }elseif ( isset($verify['statusCode']) && $verify['statusCode'] == 200 && isset($verify['data']['transaction']['errorMessage']) && $verify['data']['transaction']['errorMessage'] != null && isset($verify['data']['transaction']['status']) && ($verify['data']['transaction']['status'] == 'failed' || $verify['data']['transaction']['status'] == 'Failed')
        ) {
            $input['status'] = '0';
            $input['reason'] = $verify['data']['transaction']['errorMessage'];
        }
        elseif ( isset($verify['statusCode']) && $verify['statusCode'] == 400 && isset($verify['message']) && $verify['message'] != null) {

            $input['status'] = '0';
            $input['reason'] = $verify['message'];
        }elseif (isset($request['status']) && $request['status'] == 'waiting') {

            $input['reason'] = 'Transaction pending, please wait to get update from acquirer.';
        }
        else {

            \Log::info(['texcent_else_verify' => $verify, 'session_id' => $session_id]);
            $input['status'] = '0';
            $input['reason'] = 'Transaction declined.';
        }
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function notify(Request $request, $session_id)
    {
        $request_data = $request->all();
        \Log::info([
            'notify_inputs' => $request_data
        ]);
        // get $input data
        $input_json = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->value('request_data');

        if ($input_json == null) {
            exit();
        }
        $input = json_decode($input_json, true);
        if (isset($request_data['status']) && $request_data['status'] == 'successful') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';
        }elseif ( isset($request_data['status']) && ($request_data['status'] == 'failed' || $request_data['status'] == 'Failed') && isset($request_data['finalResponseMsg']) && $request_data['finalResponseMsg'] != null) {
            $input['status'] = '0';
            if (isset($request_data['errorMessage']) && $request_data['errorMessage'] != null) {
                $input['reason'] = $request_data['errorMessage'];
            } else {
                $input['reason'] = $request_data['finalResponseMsg'];
            }
        }
        elseif (isset($request_data['status']) && $request_data['status'] == 'waiting') {
            exit();
        }
        else {
            $input['status'] = '0';
            $input['reason'] = 'Transaction declined.';
        }
        $transaction_response = $this->storeTransaction($input);
        exit();
    }

    public function curlPost($url, $data, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_body = curl_exec($ch);
        curl_close ($ch);
        return $response_body;
    }

    public function curlGet($url, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_body = curl_exec($ch);
        curl_close ($ch);
        return $response_body;
    }
}
