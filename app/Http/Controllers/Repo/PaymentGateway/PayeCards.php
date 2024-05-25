<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\StoreTransaction;
use App\TransactionSession;
use App\Transaction;

class PayeCards extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://payment-api.payecards.com/post'; // live mode

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
        $key = $check_assign_mid->key;
        $password = $check_assign_mid->password;

        $data = [
            'action' => 'SALE',
            'client_key' => $key,
            'order_id' => $input['session_id'],
            'order_currency' => $input["converted_currency"],
            'order_amount' => number_format($input["converted_amount"],2),
            'order_description' => 'test',
            'card_number' => $input['card_no'],
            'card_exp_month' => $input['ccExpiryMonth'],
            'card_exp_year' => $input['ccExpiryYear'],
            'card_cvv2' => $input['cvvNumber'],
            'payer_first_name' => $input['first_name'],
            'payer_last_name' => $input['last_name'],
            'payer_ip' => $input['ip_address'],
            'payer_email' => $input['email'],
            'payer_phone' => $input['phone_no'],
            'payer_country' => $input['country'],
            'payer_state' => $input['state'],
            'payer_city' => $input['city'],
            'payer_address' => $input['address'],
            'payer_zip' => $input['zip'],
            'term_url_3ds' => route('payecards.return',$input['session_id']),
            'hash' => md5(strtoupper(strrev($input['email']).$password.strrev(substr($input['card_no'],0,6).substr($input['card_no'],-4))))
        ];

        \Log::info([
            'payecards-request' => $data
        ]);

        $request_url = self::BASE_URL;

        $response = $this->curlPostRequest($request_url, $data);
        
        // dd($response);
        \Log::info([
            'payecards-response' => $response
        ]);

        $input['gateway_id'] = $response->trans_id ?? null;
        $this->updateGatewayResponseData($input, $response);

        if ($response->result == 'SUCCESS') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was processed successfully.';
        } else if ($response->result == 'REDIRECT') {
            if($response->redirect_params){
                $params = (array) $response->redirect_params;
                $params['redirect_url'] = $response->redirect_url;
                $redirect_url = route('payecards.redirect',$params);
            }else{
                $redirect_url = $response->redirect_url;
            }
            $input['status'] = '7';
            $input['reason'] = '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.';
            $input['redirect_3ds_url'] = $redirect_url;
        } else if ($response->result == 'DECLINED') {
            $input['status'] = '0';
            $input['reason'] = $response->decline_reason;
        } else {
            $input['status'] = '0';
            $input['reason'] = $response->error_message ? $response->error_message : 'Transaction failed.';
        }

        return $input;
    }

    public function redirect(Request $request)
    {
        $input = $request->all();
        return view('gateway.payecards.input', compact('input'));
    }

    public function return(Request $request, $session_id)
    {
        $response = $request->all();
        \Log::info([
            'payecards-return' => $response,
            'session_id' => $session_id
        ]);

        // dd($response);
        if (!empty($session_id)) {
            $transaction_session = DB::table('transaction_session')
                ->where('transaction_id', $session_id)
                ->first();
            if ($transaction_session == null) {
                $error = 'Transaction not found.';
            }

            $input = json_decode($transaction_session->request_data, 1);

            $check_assign_mid = checkAssignMid($input['payment_gateway_id']);

            $data = [
                'action' => 'GET_TRANS_STATUS',
                'client_key' => $check_assign_mid->key,
                'trans_id' => $input['gateway_id'],
                'hash' => md5(strtoupper(strrev($input['email']).$check_assign_mid->password.$input['gateway_id'].strrev(substr($input['card_no'],0,6).substr($input['card_no'],-4))))
            ];
            $request_url = self::BASE_URL;
            $response = $this->curlPostRequest($request_url, $data);

            if ($response->status == "SETTLED") {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
            } else if ($response->status == "DECLINED") {
                $input['status'] = '0';
                $input['reason'] = (isset($response->decline_reason) ? $response->decline_reason : 'Your transaction could not processed.');
            } else if ($response->status == "REDIRECT") {
                $input['status'] = '2';
                $input['reason'] = 'Transaction is being processed.';
            } else if ($response->status == "PENDING") {
                $input['status'] = '2';
                $input['reason'] = 'Transaction is in pending.';
            } else {
                $input['status'] = '2';
                $input['reason'] = 'Transaction is in pending';
            }
            unset($input["reqest_data"]);
            
            // $this->updateGatewayResponseData($input, $response);
            // store transaction
            // $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        }
    }


    public function notify(Request $request)
    {
        $response = $request->all();
        \Log::info([
            'payecards-notify' => $response
        ]);

        $session_id = $response['order_id'];
        if (!empty($session_id)) {
            $transaction_session = DB::table('transaction_session')
                ->where('transaction_id', $session_id)
                ->first();
            if ($transaction_session == null) {
                $error = 'Transaction not found.';
            }
            $input = json_decode($transaction_session->request_data, 1);
            if ($response["status"] == "SETTLED") {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
            } else if ($response["status"] == "DECLINED") {
                $input['status'] = '0';
                $input['reason'] = (isset($response['decline_reason']) ? $response['decline_reason'] : 'Your transaction could not processed.');
            } else if ($response["status"] == "ERROR") {
                $input['status'] = '0';
                $input['reason'] = 'Request has errors and was not validated by Payment Platform';
            } else {
                $input['status'] = '2';
                $input['reason'] = 'Transaction is in pending';
            }
            unset($input["reqest_data"]);
            // $input['gateway_id'] = $response['tradeNo'] ?? null;
            $this->updateGatewayResponseData($input, $response);
            // store transaction
            $transaction_response = $this->storeTransaction($input);
            exit();
        }
    }

    public function curlPostRequest($url, $data)
    {
        
        $headers = array(
            'Content-Type: multipart/form-data',
        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        // curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            $headers
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }
}
