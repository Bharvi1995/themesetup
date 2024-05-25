<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Mail;
use Session;
use Exception;
use App\User;
use App\Transaction;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Cartalyst\Stripe\Laravel\Facades\Stripe;


class OpayGateway extends Controller
{
    use StoreTransaction;
    const BASE_URL = 'https://cashierapi.opayweb.com/api/v3'; // live
    // const BASE_URL = 'http://sandbox-cashierapi.opayweb.com/api/v3'; // test

    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     $this->user = new User;
    //     // $this->Transaction = new Transaction;
    // }

    // ================================================
    /* method : checkout
     * @param  : 
     * @Description : transaction by inline js API
     */// ==============================================
    public function checkout($input, $check_assign_mid)
    {
        $request_data = [
            "reference" => $input['session_id'],
            "amount" => $input['converted_amount']*100,
            "currency" => $input['converted_currency'],
            // // "country" => 'NG',
            "country" => $input['country'],
            "payType" => 'bankcard',
            "firstName" => $input['first_name'],
            "lastName" => $input['last_name'],
            "customerEmail" => $input['email'],
            "customerPhone" => $input['phone_no'],
            "cardNumber" => $input['card_no'],
            "cardDateMonth" => $input['ccExpiryMonth'],
            "cardDateYear" => substr($input['ccExpiryYear'], -2),
            "cardCVC" => $input['cvvNumber'],
            // // "bankAccountNumber" => $request['bankAccountNumber'],
            // // "bankCode" => $request['bankCode'],
            "bankAccountNumber" => null,
            "bankCode" => null,
            "billingZip" => $input['zip'],
            "billingCity" => $input['city'],
            "billingAddress" => $input['address'],
            "billingState" => $input['state'],
            "billingCountry" => $input['country'],
            "return3dsUrl" => route('opay.redirect', $input['session_id']),
            "reason" => 'live',
        ];
        \Log::info([
            'opayRequestData' => $request_data
        ]);
        // dd('yes');
        ksort($request_data);

        $request_url = self::BASE_URL.'/transaction/initialize';

        $request_headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$check_assign_mid->public_key,
            'MerchantId: '.$check_assign_mid->merchant_id,
        ];

        $payload = json_encode($request_data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        $response_body = curl_exec($ch);

        curl_close ($ch);

        $response_data = json_decode($response_body, 1);
        // dd($response_data);
        \Log::info([
            'opay_initialize' => $response_data
        ]);

        // update session data
        if(isset($response_data['data']['orderNo'])) {
            $input['gateway_id'] = $response_data['data']['orderNo'] ?? null;
            $this->updateGatewayResponseData($input, $response_data);
        }

        // status successful
        if (isset($response_data['data']['status']) && $response_data['data']['status'] == 'SUCCESS') {

            return [
                'status' => '1',
                'reason' => 'Your transaction has been processed successfully.',
                'order_id' => $input['order_id'],
            ];

        // transaction pending 
        } elseif (isset($response_data['data']['status']) && in_array($response_data['data']['status'], ['PENDING', 'INITIAL'])) {

            // sending blade file and waiting 15 seconds
            // redirect 3ds page
            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                'redirect_3ds_url' => route('opay.pendingBlade', [$input['session_id'], $input['gateway_id'], '1']),
            ];

        // redirect to acquirer server
        } elseif (isset($response_data['data']['status']) && $response_data['data']['status'] == '3DSECURE') {

            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                'redirect_3ds_url' => $response_data['data']['authURL'],
            ];

        // redirect for otp, pin or password
        } elseif (isset($response_data['data']['status']) && in_array($response_data['data']['status'], ['INPUT-PIN', 'INPUT-OTP', 'INPUT-PHONE'])) {

            if ($response_data['data']['status'] == 'INPUT-PIN') {
                $input_type = 'pin';
            } elseif ($response_data['data']['status'] == 'INPUT-OTP') {
                $input_type = 'otp';
            } elseif ($response_data['data']['status'] == 'INPUT-PHONE') {
                $input_type = 'phone';
            }

            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                'redirect_3ds_url' => route('opay.inputForm', [$input_type, $input['session_id'], $response_data['data']['orderNo']]),
            ];

        // declined
        } elseif (isset($response_data['data']['status']) && in_array($response_data['data']['status'], ['FAIL', 'CLOSE'])) {

            return [
                'status' => '0',
                'reason' => $response_data['data']['failureReason'] ?? 'Transaction authentication failed.',
                'order_id' => $input['order_id'],
            ];
            
        } else {

            return [
                'status' => '0',
                'reason' => $response_data['message'] ?? 'Transaction authentication failed.',
                'order_id' => $input['order_id'],
            ];

        }
        
    }

    // ================================================
    /* method : pendingBlade
     * @param  : 
     * @Description : wait for 15 seconds in blade
     */// ==============================================
    public function pendingBlade($session_id, $order_id, $loop_no)
    {
        $input = DB::table('transaction_session')
                ->where('transaction_id', $session_id)
                ->value('request_data');

        if ($input != null) {
            $input = json_decode($input, true);
        } else {
            return abort('404');
        }

        return view('gateway.opay.pendingBlade', compact('order_id', 'session_id', 'loop_no'));
    }

    // ================================================
    /* method : pendingBladeSubmit
    * @param  : 
    * @description : submit pending blade
    */// ==============================================
    public function pendingBladeSubmit(Request $request, $session_id, $order_id, $loop_no)
    {
        // get $input data
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($input_json == null) {
            return abort(404);
        }

        $input = json_decode($input_json['request_data'], true);

        $check_assign_mid = checkAssignMid($input['payment_gateway_id']);

        $status_url = self::BASE_URL.'/transaction/status';

        $status_data = [
            'orderNo' => $order_id,
            'reference' => $session_id,
        ];

        $signature = hash_hmac('sha512', json_encode($status_data), $check_assign_mid->secret_key);

        $status_headers = [
            'Authorization: Bearer '.$signature,
            'MerchantId: '.$check_assign_mid->merchant_id,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $status_url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($status_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $status_headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $status_body = curl_exec($ch);

        curl_close ($ch);

        $status_response = json_decode($status_body, true);
        // \Log::info([
        //     'pending_status_check' => $status_response
        // ]);

        // status successful
        if (isset($status_response['data']['status']) && $status_response['data']['status'] == 'SUCCESS') {

            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';

        // transaction pending 
        } elseif (isset($status_response['data']['status']) && in_array($status_response['data']['status'], ['PENDING', 'INITIAL'])) {

            // 3 time check pending to status check then 
            if ($loop_no == 3) {
                $input['status'] = '2';
                $input['reason'] = 'Transaction is pending in acquirer system, please check after few minutes.';
            } else {
                $loop_no = $loop_no + 1;

                return redirect()->route('opay.pendingBlade', [$input['session_id'], $input['gateway_id'], $loop_no]);
            }

        // redirect to acquirer server
        } elseif (isset($status_response['data']['status']) && $status_response['data']['status'] == '3DSECURE') {

            return redirect($status_response['data']['authURL']);

        // redirect for otp, pin or password
        } elseif (isset($status_response['data']['status']) && in_array($status_response['data']['status'], ['INPUT-PIN', 'INPUT-OTP', 'INPUT-PHONE'])) {

            if ($status_response['data']['status'] == 'INPUT-PIN') {
                $input_type = 'pin';
            } elseif ($status_response['data']['status'] == 'INPUT-OTP') {
                $input_type = 'otp';
            } elseif ($status_response['data']['status'] == 'INPUT-PHONE') {
                $input_type = 'phone';
            }

            return redirect()->route('opay.inputForm', [$input_type, $input['session_id'], $status_response['data']['orderNo']]);

        // declined
        } elseif (isset($status_response['data']['status']) && in_array($status_response['data']['status'], ['FAIL', 'CLOSE'])) {

            $input['status'] = '0';
            $input['reason'] = $status_response['data']['failureReason'] ?? 'Transaction authentication failed.';
            
        } else {

            $input['status'] = '0';
            $input['reason'] = $status_response['message'] ?? 'Transaction authentication failed.';

        }
        $input["is_webhook"] = "4";
        // redirect back to $response_url
        $transaction_response = $this->storeTransaction($input);
        // unset($input['api_key']);
        // $this->Transaction->storeData($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

        // ================================================
    /* method : inputForm
     * @param  : 
     * @Description : input otp, pin or phone
     */// ==============================================
    public function inputForm($input_type, $session_id, $order_id)
    {
        $input = DB::table('transaction_session')
                ->where('transaction_id', $session_id)
                ->value('request_data');

        if ($input != null) {
            $input = json_decode($input, true);
        } else {
            return abort('404');
        }

        return view('gateway.opay.input', compact('order_id', 'session_id', 'input_type'));
    }

    // ================================================
    /* method : inputResponse
    * @param  : 
    * @description : form submit
    */// ==============================================
    public function inputResponse(Request $request, $input_type, $session_id, $order_id)
    {
        $this->validate($request, [
            'input' => 'required',
        ]);

        // get $input data
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($input_json == null) {
            return abort(404);
        }

        $input = json_decode($input_json['request_data'], true);
        $input['customer_order_id'] = $input['customer_order_id'] ?? null;

        $session_response = json_decode($input_json['response_data'], true);
        // dd($input['response_data']);
        $order_id = $session_response['data']['orderNo'];

        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

        $request_data = [
            "orderNo" => $order_id,
            "reference" => $session_id,
            $input_type => $request->input,
        ];

        $request_url = self::BASE_URL.'/transaction/input-'.$input_type;

        $request_headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$check_assign_mid->public_key,
            'MerchantId: '.$check_assign_mid->merchant_id,
        ];

        $payload = json_encode($request_data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        $response_body = curl_exec($ch);

        curl_close ($ch);

        $response_data = json_decode($response_body, 1);
        \Log::info([
            'after_pin_reponse' => $response_data
        ]);

        // status successful
        if (isset($response_data['data']['status']) && $response_data['data']['status'] == 'SUCCESS') {

            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';

        // transaction pending 
        } elseif (isset($response_data['data']['status']) && in_array($response_data['data']['status'], ['PENDING', 'INITIAL'])) {

            return redirect()->route('opay.pendingBlade', [$input['session_id'], $input['gateway_id'], '1']);

        // redirect to acquirer server
        } elseif (isset($response_data['data']['status']) && $response_data['data']['status'] == '3DSECURE') {

            return redirect($response_data['data']['authURL']);

        // redirect for otp, pin or password
        } elseif (isset($response_data['data']['status']) && in_array($response_data['data']['status'], ['INPUT-PIN', 'INPUT-OTP', 'INPUT-PHONE'])) {

            if ($response_data['data']['status'] == 'INPUT-PIN') {
                $input_type = 'pin';
            } elseif ($response_data['data']['status'] == 'INPUT-OTP') {
                $input_type = 'otp';
            } elseif ($response_data['data']['status'] == 'INPUT-PHONE') {
                $input_type = 'phone';
            }

            return redirect()->route('opay.inputForm', [$input_type, $input['session_id'], $order_id]);

        // declined
        } elseif (isset($response_data['data']['status']) && in_array($response_data['data']['status'], ['FAIL', 'CLOSE'])) {

            $input['status'] = '0';
            $input['reason'] = $response_data['data']['failureReason'] ?? 'Transaction authentication failed.';
            
        } else {
            \Log::info(['opay_response_else' => $response_data]);

            $input['status'] = '0';
            $input['reason'] = $response_data['message'] ?? 'Transaction authentication failed.';

        }
        $input["is_webhook"] = "5";
         // redirect back to $response_url
        $transaction_response = $this->storeTransaction($input);
        // unset($input['api_key']);
        // $this->Transaction->storeData($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    // ================================================
    /* method : redirect
    * @param  : 
    * @description : redirect back after 3ds
    */// ==============================================
    public function redirect(Request $request, $session_id)
    {
        // get $input data
        $input_json = TransactionSession::where('transaction_id', $session_id)
            // ->where('is_completed', '0')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($input_json == null) {
            return abort(404);
        }

        $input = json_decode($input_json['request_data'], true);
        $input['customer_order_id'] = $input['customer_order_id'] ?? null;
        
        $session_response = json_decode($input_json['response_data'], true);
        $order_id = $session_response['data']['orderNo'];

        $check_assign_mid = checkAssignMid($input['payment_gateway_id']);

        $status_url = self::BASE_URL.'/transaction/status';

        $status_data = [
            'orderNo' => $order_id,
            'reference' => $input['session_id'],
        ];

        $signature = hash_hmac('sha512', json_encode($status_data), $check_assign_mid->secret_key);

        $status_headers = [
            'Authorization: Bearer '.$signature,
            'MerchantId: '.$check_assign_mid->merchant_id,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $status_url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($status_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $status_headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $status_body = curl_exec($ch);

        curl_close ($ch);

        $status_response = json_decode($status_body, true);

        // status successful
        if (isset($status_response['data']['status']) && $status_response['data']['status'] == 'SUCCESS') {

            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';

        // transaction pending 
        } elseif (isset($status_response['data']['status']) && in_array($status_response['data']['status'], ['PENDING', 'INITIAL'])) {

            $input['status'] = '2';
            $input['reason'] = 'Transaction is pending in acquirer system, please check after few minutes.';

        // redirect to acquirer server
        } elseif (isset($status_response['data']['status']) && $status_response['data']['status'] == '3DSECURE') {

            $input['status'] = '2';
            $input['reason'] = 'Transaction is pending for authentication, please check after few minutes.';

        // declined
        } elseif (isset($status_response['data']['status']) && in_array($status_response['data']['status'], ['FAIL', 'CLOSE'])) {

            $input['status'] = '0';
            $input['reason'] = $status_response['data']['failureReason'] ?? 'Transaction authentication failed.';
            
        } else {
            \Log::info(['opay_response_else' => $status_response]);

            $input['status'] = '0';
            $input['reason'] = $status_response['message'] ?? 'Transaction authentication failed.';

        }
        $input["is_webhook"] = "6";
        // redirect back to $response_url
        $transaction_response = $this->storeTransaction($input);
        // unset($input['api_key']);
        // $this->Transaction->storeData($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    // ================================================
    /* method : notify
    * @param  : 
    * @description : server notification
    */// ==============================================
    public function notify(Request $request)
    {
        // get $input data
        // $input_json = TransactionSession::where('transaction_id', $request['payload']['reference'])
        //     // ->where('is_completed', '0')
        //     ->first();
        
        // if ($input_json == null) {
        //     exit();
        // }

        // http_response_code(200);

        // $request_data = $request->all();
        // // \Log::info([
        // //     'opay_notification' => $request_data,
        // // ]);

        // $input = json_decode($input_json['request_data'], true);
        // $input['is_webhook'] = '1';

        // // transaction was successful...
        // if (isset($request_data['payload']['status']) && $request_data['payload']['status'] == 'successful') {

        //     $input['status'] = '1';
        //     $input['reason'] = 'Your transaction was proccessed successfully.';

        //     // store transaction
        //     // $this->Transaction->storeData($input, $input['is_request_from_vt']);

        // // if transaction pending
        // } elseif (isset($request_data['payload']['status']) && in_array($request_data['payload']['status'], ['PENDING', 'INITIAL', '3DSECURE', 'INPUT-PIN', 'INPUT-OTP', 'INPUT-PHONE'])) {

        //     exit();

        // // if transaction declined
        // } elseif (isset($request_data['payload']['status']) && in_array($request_data['payload']['status'], ['FAIL', 'failed', 'CLOSE'])) {

        //     $input['status'] = '0';
        //     if (isset($request_data['description']) && $request_data['description'] != null) {
        //         $input['reason'] = $request_data['description'];
        //     } elseif (isset($request_data['payload']['displayedFailure']) && $request_data['payload']['displayedFailure'] != null) {
        //         $input['reason'] = $request_data['payload']['displayedFailure'];
        //     } else {
        //         $input['reason'] = 'TRANSACTION FAILED.';
        //     }

        //     // store transaction
        //     // $this->Transaction->storeData($input, $input['is_request_from_vt']);

        // // if transaction canceled
        // } elseif (isset($request_data['error']) && $request_data['error'] != null) {

        //     $input['status'] = '0';
        //     $input['reason'] = $request_data['description'] ?? 'Transaction canceled.';

        //     // store transaction
        //     // $this->Transaction->storeData($input, $input['is_request_from_vt']);

        // } else {

        //     \Log::info(['opay_notify_else' => $request_data]);
        //     exit();
        // }

        // update transaction_session record
        // try {
        //     DB::table('transaction_session')
        //         ->where('transaction_id', $input['session_id'])
        //         ->update([
        //             'is_completed' => '1'
        //         ]);
        // } catch(\Exception $e) {

        // }
        // $transaction_response = $this->storeTransaction($input);

        exit();
    }

    // ================================================
    /* method : cronjob
    * @param  : 
    * @description : set cronjob
    */// ==============================================
    public function cronjob(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sShiu235Ha') {
            exit();
        }
        exit();
        $one_hour_ago = date('Y-m-d H:i:s', strtotime('-1 hour'));

        // get $input data
        $gateway_ids = \DB::table('transaction_session')
            ->where('payment_gateway_id', '234')
            ->where('created_at', '<=', $one_hour_ago)
            ->whereNotNull('gateway_reference_id')
            ->where('is_completed', '0')
            ->pluck('gateway_reference_id')
            ->toArray();

        \Log::info([
            'pending_session' => $gateway_ids
        ]);

        foreach ($gateway_ids as $value) {

            // get $input data
            $input_json = TransactionSession::where('gateway_reference_id', $value)
                ->where('is_completed', '0')
                ->first();
            
            if ($input_json == null) {
                continue;
            }

            $input = json_decode($input_json['request_data'], true);
            
            $check_assign_mid = checkAssignMid($input['payment_gateway_id']);

            $status_url = self::BASE_URL.'/transaction/status';

            $status_data = [
                'orderNo' => $value,
                'reference' => $input['session_id'],
            ];

            $signature = hash_hmac('sha512', json_encode($status_data), $check_assign_mid->secret_key);

            $status_headers = [
                'Authorization: Bearer '.$signature,
                'MerchantId: '.$check_assign_mid->merchant_id,
                'Content-Type: application/json',
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $status_url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($status_data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $status_headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $status_body = curl_exec($ch);

            curl_close ($ch);

            $status_response = json_decode($status_body, true);
            \Log::info([
                'opay_status' => $status_response,
            ]);
            // status successful
            if (isset($status_response['data']['status']) && $status_response['data']['status'] == 'SUCCESS') {

                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccessed successfully.';

            // transaction pending 
            } elseif (isset($status_response['data']['status']) && in_array($status_response['data']['status'], ['PENDING', 'INITIAL'])) {

                $input['status'] = '2';
                $input['reason'] = 'Transaction is pending in acquirer system, please check after few minutes.';

            // redirect to acquirer server
            } elseif (isset($status_response['data']['status']) && $status_response['data']['status'] == '3DSECURE') {

                // $input['status'] = '2';
                // $input['reason'] = 'Transaction is pending for authentication, please check after few minutes.';
                $input['status'] = '0';
                $input['reason'] = 'Transaction timeout.';

            // declined
            } elseif (isset($status_response['data']['status']) && in_array($status_response['data']['status'], ['FAIL', 'CLOSE'])) {

                $input['status'] = '0';
                $input['reason'] = $status_response['data']['failureReason'] ?? 'Transaction authentication failed.';
                
            } else {
                \Log::info(['opay_cron_else' => $status_response]);

                $input['status'] = '0';
                $input['reason'] = $status_response['message'] ?? 'Transaction authentication failed.';

            }

            // store transaction
            // $this->Transaction->storeData($input, $input['is_request_from_vt']);

            // update transaction_session record if not pending
            if ($input['status'] != '2') {
                
                \DB::table('transaction_session')
                    ->where('transaction_id', $input['session_id'])
                    ->update(['is_completed' => '1']);
            }
        }

        // cron completed
        \Log::info('opay_cron_completed');
        exit();
    }

    public function getOpayStatus(Request $request)
    {
        $input = $request->all();

        $status_url = self::BASE_URL.'/transaction/status';

        $secret_key = 'OPAYPRV16159787289420.42750853510852016';
        $merchant_id = '256621031722851';

        $status_data = [
            'orderNo' => $input['gateway_reference_id'],
            'reference' => $input['session_id'],
        ];

        $signature = hash_hmac('sha512', json_encode($status_data), $secret_key);

        $status_headers = [
            'Authorization: Bearer '.$signature,
            'MerchantId: '.$merchant_id,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $status_url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($status_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $status_headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $status_body = curl_exec($ch);

        curl_close ($ch);

        $status_response = json_decode($status_body, true);

        dd($status_response);
    }
}