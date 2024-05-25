<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Illuminate\Support\Facades\Crypt;
use Mail;
use Session;
use Exception;
use DateTime;
use App\User;
use App\Transaction;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use App\TransactionSession;

class SecureePayment extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://api.secureepayments.com/post'; // Live

    public function __construct() {
        $this->user = new User;
        $this->transaction = new Transaction;
    }
    // ================================================
    /* method : Checkout
    * @param  :
    * @Description : Card
    */// ==============================================
    public function checkout($input, $check_assign_mid)
    {
        // echo"test"; exit;
        $access_key =$check_assign_mid->merchant_id;     // The access key received from Rapyd.
        $secret_key =$check_assign_mid->secret_key;      // Never transmit the secret key by itself.


       $hash = md5(strtoupper(strrev($input['email']).$secret_key.
            strrev(substr($input['card_no'],0,6).substr($input['card_no'],-4))));

        /******** payment_method **********/

        $request_data = [
            "action" => 'SALE',
            "client_key" => $access_key,
            "order_id" => $input['order_id'],
            "order_amount" => number_format($input['converted_amount'],'2','.',''),
            "order_currency" => $input['currency'],
            "order_description" => $input['order_id'],
            "card_number" => $input['card_no'],
            "card_exp_month" => $input['ccExpiryMonth'],
            "card_exp_year" => $input['ccExpiryYear'],
            "card_cvv2" => $input['cvvNumber'],
            "payer_first_name" => $input['first_name'],
            "payer_last_name" => $input['last_name'],
            "payer_address" => $input['address'],
            "payer_country" => $input['country'],
            "payer_state" => $input['state'],
            "payer_city" => $input['city'],
            "payer_zip" => $input['zip'],
            "payer_email" => $input['email'],
            "payer_phone" => $input['phone_no'],
            "payer_ip" => $input['ip_address'],
            "term_url_3ds" => route('notification-secure-epayment'),
            "hash" => $hash
        ];
        $request_query = http_build_query($request_data);
        $gateway_url = self::BASE_URL;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_URL, $gateway_url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request_query);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        $request_response = json_decode($response, true);
        \Log::info([
            'SecureePay_request_response' => $request_response
        ]);

        $orderNo = $input['order_id'];
        // update session data
        if(isset($request_response['status'])) {
            $input['gateway_id'] = $request_response['trans_id'] ?? null;
            $this->updateGatewayResponseData($input, $request_response);
        }
        if(isset($request_response['result'])) {
            if($request_response['result']=='ERROR') {
                $input['status'] = '0';
                $input['reason'] = (!empty($request_response['error_message']) ? $request_response['error_message'] : 'Your transaction could not processed.');
            }else if($request_response['result']=='DECLINED') {
                $input['status'] = '0';
                $input['reason'] = (!empty($request_response['decline_reason']) ? $request_response['decline_reason'] : 'Your transaction could not processed.');
            }else if($request_response['result'] == 'SUCCESS') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was processed successfully.';
            }else if($request_response['result'] == '3DS') {
                $input['status'] = '7';
                $input['reason'] = 'The transaction awaits 3D-Secure validation.';
            } else if($request_response['result'] == 'PENDING') {
                $input['status'] = '7';
                $input['reason'] = 'The transaction awaits CAPTURE.';
            } else if($request_response['result'] == 'PREPARE') {
                $input['status'] = '7';
                $input['reason'] = 'Status is undetermined, final status will be sent in callback.';
            }else if($request_response['result']=='REDIRECT') {
                $input['status'] = '7';
                $input['reason'] = '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.';
                $input['redirect_3ds_url'] = "".$request_response['redirect_url'];
                \Session::put('tra_session_id',$input['session_id']);
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                    'redirect_3ds_url' => route('secureepayments-inputResponse', $input['session_id']),
                ];

            }   else {
                $input['status'] = '0';
                $input['reason'] = 'Your transaction could not processed.';
                $message = $input['reason'];
            }
        }
        else {
            $input['status'] = '0';
            $input['reason'] = 'Your transaction could not processed.';
        }
        return $input;
    }


    public function inputResponse(Request $request, $session_id)
    {
        $request_data = $request->all();
        $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first();

        if ($transaction_session == null) {
            return response()->json(['message' => 'Transaction not found.']);
        }
        $response_data = json_decode($transaction_session->response_data, 1);
        return view('gateway.secureepayments.input', compact('session_id', 'response_data'));
    }
    
    public function notification(Request $request) {
        \Log::info([
            'notification-secure-epayment-get' => $request->all(),
            'session_id' => \Session::get('tra_session_id')
        ]);
        $session_id = \Session::get('tra_session_id');
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($input_json == null) {
            return abort(404);
        }
        sleep(5);
        $transaction = Transaction::select("status","reason")->where("session_id",$session_id)->first();
        \Log::info([
            'transaction' => $transaction
        ]);
        $input = json_decode($input_json->request_data, true);
        $input["status"] = $transaction->status;
        $input["reason"] = $transaction->reason;
        $store_transaction_link = $this->getRedirectLink($input);
        \Session::forget('tra_session_id'); 
        return redirect($store_transaction_link);
    }


    public function postnotification(Request $request) {
        // \Log::info([
        //     'notification-secure-epayment' => $request->all()
        // ]);

        $request_response = $request->all();

        // \Log::info([
        //     'Securee_callback_response' => $request_response
        // ]);
        if(isset($request_response['order_id'])) {
            $transaction_session = DB::table('transaction_session')
                ->where('order_id', $request_response['order_id'])
                ->first();

            if ($transaction_session == null) {
                return response()->json(['message' => 'Transaction not found.']);
            }

            if ($request_response['result'] == 'SUCCESS') {
                $input = json_decode($transaction_session->request_data, 1);
                $input['reason'] = $request_response['status'];
                $input['status'] = 1;
            } else {
                if (isset($request_response['error_message'])) {
                    $input['reason'] = $request_response['error_message'];
                    $input['status'] = 0;
                } else {
                    $input['reason'] = $request_response['result'];
                    $input['status'] = 0;
                }
            }
            \Log::info([
                'input_callback' => $input
            ]);
            // store transaction
            $transaction_response = $this->storeTransaction($input);
            exit();
            // $store_transaction_link = $this->getRedirectLink($input);
            // return redirect($store_transaction_link);
        }
    }

}
