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

class Wyre extends Controller
{
    use StoreTransaction;
    const BASE_URL = 'https://api.testwyre.com'; // test
    //const BASE_URL = 'https://api.sendwyre.com'; // live

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

    // ================================================
    /* method : checkout
     * @param  : 
     * @Description : return link
     */ // ==============================================
    public function checkout($input, $check_assign_mid)
    {
        \Log::info([
            'wyre_checkout' => $input,
        ]);
        $reserve_url = self::BASE_URL . '/v3/orders/reserve';
        $reserve_header = [
            'Authorization: Bearer ' . $check_assign_mid->secret_key,
            'Content-Type: application/json',
        ];

        if (isset($input['country_code']) && $input['country_code'] != '') {
            $country_code = $input['country_code'];
        } else {
            $country_code = '';
        }

        if (substr($input['phone_no'], 0, 1) == '+')
            $payer_phone = $country_code . $input['phone_no'];
        else
            $payer_phone = '+' . $country_code . $input['phone_no'];

        $reserve_data = [
            "amount" => $input['converted_amount'],
            "sourceCurrency" => $input['currency'], // USD, CAD, EUR, GBP and AUD
            "destCurrency" => 'BTC',
            "dest" => $check_assign_mid->dest,
            "firstName" => $input['first_name'],
            "lastName" => $input['last_name'],
            "phone" => $payer_phone,
            "email" => $input['email'],
            "country" => $input['country'],
            "postalCode" => $input['zip'],
            "state" => $input['state'],
            "city" => $input['city'],
            "street1" => $input['address'],
            "redirectUrl" => route('Wyre.redirect', $input['session_id']),
            "referrerAccountId" => $check_assign_mid->reference_id,
            "referenceId" => $input['session_id'],
        ];

        $reserve_json_data = json_encode($reserve_data);

        // create wallet reserve
        try {
            $reserve_json = $this->curlPost($reserve_url, $reserve_json_data, $reserve_header);
        } catch (\Exception $e) {
            // 
        }

        $reserve_response = json_decode($reserve_json, true);
        \Log::info([
            'order_reserve_response' => $reserve_response,
        ]);
        // success reservation id generate
        if (isset($reserve_response['reservation']) && $reserve_response['reservation'] != null) {

            $card_url = self::BASE_URL . '/v3/debitcard/process/partner';

            $card_header = [
                'Authorization: Bearer ' . $check_assign_mid->secret_key,
                'Content-Type: application/json',
            ];

            $card_data = [
                "amount" => $input['converted_amount'],
                "sourceCurrency" => $input['currency'], // USD, CAD, EUR, GBP and AUD
                "destCurrency" => 'BTC',
                "dest" => $check_assign_mid->dest,
                "givenName" => $input['first_name'],
                "familyName" => $input['last_name'],
                "phone" => $input['phone_no'],
                "email" => $input['email'],
                "address" => [
                    "country" => $input['country'],
                    "postalCode" => $input['zip'],
                    "state" => $input['state'],
                    "city" => $input['city'],
                    "street1" => $input['address'],
                ],
                "referrerAccountId" => $check_assign_mid->reference_id,
                "reservationId" => $reserve_response['reservation'],
                "debitCard" => [
                    "number" => $input['card_no'],
                    "year" => $input['ccExpiryYear'],
                    "month" => $input['ccExpiryMonth'],
                    "cvv" => $input['cvvNumber'],
                ],
                "referenceId" => $input['session_id'],
            ];

            $card_json_data = json_encode($card_data);

            // card processing request
            try {
                $card_json = $this->curlPost($card_url, $card_json_data, $card_header);
            } catch (\Exception $e) {
            }

            $card_response = json_decode($card_json, true);
            \Log::info([
                'card_response' => $card_response,
            ]);

            // update session data
            if (isset($card_response['id']) && $card_response['id'] != null) {
                $input['gateway_id'] = $card_response['id'];
                $this->updateGatewayResponseData($input, $card_response);
            }

            // if status RUNNING_CHECKS then send OTP page as 3ds
            if (isset($card_response['status']) && $card_response['status'] = 'RUNNING_CHECKS') {

                // redirect to acquirer server with data in query_string
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect.',
                    'redirect_3ds_url' => route('Wyre.form', ['session_id' => $input['session_id'], 'amount' => $input['converted_amount'], 'currency' => $input['currency'], 'walletOrderId' => $card_response['id'], 'reservation' => $reserve_response['reservation']])
                ];
                // error in card processing with error message
            } elseif (isset($card_response['errorCode']) && $card_response['errorCode'] != null) {
                $input['status'] = '0';
                $input['reason'] = $card_response['message'] ?? $card_response['errorCode'];
                // error in card processing without error message
            } else {
                $input['status'] = '0';
                $input['reason'] = $card_response['message'] ?? 'Transaction request declined.';
            }
            // error in wallet reservation with error message
        } elseif (isset($reserve_response['errorCode']) && $reserve_response['errorCode'] != null) {
            $input['status'] = '0';
            $input['reason'] = $reserve_response['message'] ?? $reserve_response['errorCode'];
            // error in wallet reservation without error message
        } else {
            $input['status'] = '0';
            $input['reason'] = $reserve_response['message'] ?? 'Transaction request declined.';
        }

        return [
            'status' => '0',
            'reason' => $input['reason'],
            'order_id' => $input['order_id'],
        ];
    }

    // ================================================
    /* method : form
    * @param  : 
    * @description : get otp form
    */ // ==============================================
    public function form(Request $request)
    {
        // $data from query_string data for next request
        $data = [
            'session_id' => $request['session_id'],
            'walletOrderId' => $request['walletOrderId'],
            'reservation' => $request['reservation'],
            'amount' => $request['amount'],
            'currency' => $request['currency'],
        ];

        return view('gateway.wyre.otp', compact('data'));
    }

    // ================================================
    /* method : submit
    * @param  : 
    * @description : submit otp form
    */ // ==============================================
    public function submit(Request $request)
    {
        $this->validate($request, [
            'session_id' => 'required',
            'walletOrderId' => 'required',
            'reservation' => 'required',
            'sms' => 'required',
        ]);

        // get $input data
        $input_json = TransactionSession::where('transaction_id', $request['session_id'])
            ->where('is_completed', '0')
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }

        $input = json_decode($input_json['request_data'], true);

        $check_assign_mid = checkAssignMid($input['payment_gateway_id']);

        $authorize_url = self::BASE_URL . '/v3/debitcard/authorize/partner';

        $authorize_header = [
            'Authorization: Bearer ' . $check_assign_mid->secret_key,
            'Content-Type: application/json',
        ];

        $authorize_data = [
            "type" => 'ALL',
            "walletOrderId" => $request['walletOrderId'],
            "sms" => $request['sms'],
            "card2fa" => "000000",
        ];

        $authorize_json_data = json_encode($authorize_data);

        // OTP verify request
        try {
            $authorize_json = $this->curlPost($authorize_url, $authorize_json_data, $authorize_header);
        } catch (\Exception $e) {
            // 
        }

        $authorize_response = json_decode($authorize_json, true);
        \Log::info(['authorize_response' => $authorize_response]);

        // if success=true then verify status
        if (isset($authorize_response['success']) && $authorize_response['success'] == true) {

            $status_url = self::BASE_URL . '/v3/orders/' . $input['gateway_id'];

            $status_header = [
                'Authorization: Bearer ' . $check_assign_mid->secret_key
            ];

            // get transaction status
            try {
                $status_json = $this->curlGet($status_url, $status_header);
            } catch (\Exception $e) {
                // 
            }

            $status_response = json_decode($status_json, true);
            \Log::info(['status_response' => $status_response]);

            // transaction success
            if (isset($status_response['status']) && $status_response['status'] == 'COMPLETE') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccessed successfully.';
                // pending
            } elseif (
                isset($status_response['status']) &&
                in_array($status_response['status'], ['PROCESSING', 'RUNNING_CHECKS'])
            ) {
                $getStatusAgain = $this->curlStatusGet($status_url, $status_header, 1);
                \Log::info(['status_response_2' => $getStatusAgain]);

                if (
                    isset($getStatusAgain['response']['status']) &&
                    in_array($getStatusAgain['response']['status'], ['PROCESSING', 'COMPLETE'])
                ) {
                    $input['status'] = '1';
                    $input['reason'] = 'Your transaction was proccessed successfully.';
                } else {
                    $input['status'] = '2';
                    $input['reason'] = 'Your transaction is pending confirmation from the bank . It will be confirmed within next 5 minutes and you will be able to check the updated status in your dashboard.';
                }

                // declined with error
            } elseif (isset($status_response['status']) && $status_response['status'] == 'FAILED') {
                $input['status'] = '0';
                $input['reason'] = $status_response['errorMessage'] ?? 'Transaction declined.';
                // declined without error
            } else {
                $input['status'] = '0';
                $input['reason'] = $status_response['message'] ?? 'Transaction declined.';
            }

            // redirect back to $response_url
            $transaction_response = $this->storeTransaction($input);
            // unset($input['api_key']);
            // $this->Transaction->storeData($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);

            // error in otp verify with error message
        } elseif (isset($authorize_response['errorCode']) && $authorize_response['errorCode'] != null) {
            $input['status'] = '0';
            $input['reason'] = $authorize_response['message'] ?? $authorize_response['errorCode'];
            // error in otp verify without error message
        } else {
            $input['status'] = '0';
            $input['reason'] = $authorize_response['message'] ?? 'Transaction request declined.';
        }

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
    * @description : receive webhooks
    */ // ==============================================
    public function notify(Request $request)
    {
        \Log::info(['wyre_webhooks' => $request->all()]);
        http_response_code(200);

        // check in transactions table
        $same_transaction = DB::table('transactions')
            ->where('session_id', $request['referenceId'])
            ->where('status', '!=', '2')
            ->first();

        if ($same_transaction != null) {
            exit();
        }

        // get $input data
        $input_json = DB::table('transaction_session')
            ->where('transaction_id', $request['referenceId'])
            ->where('is_completed', '0')
            ->value('request_data');

        if ($input_json == null) {
            exit();
        }

        $input = json_decode($input_json, true);
        $input['is_webhook'] = '1';

        // wait for 10 seconds
        sleep(10);

        // transaction was successful...
        if (isset($request['orderStatus']) && $request['orderStatus'] == 'COMPLETE') {

            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';

            // store transaction
            $this->Transaction->storeData($input, $input['is_request_from_vt']);

            // if transaction declined with reason
        } elseif (isset($request['orderStatus']) && $request['orderStatus'] == 'FAILED') {
            $input['status'] = '0';
            $input['reason'] = $request['failedReason'] ?? 'Transaction declined.';

            // store transaction
            $this->Transaction->storeData($input, $input['is_request_from_vt']);

            // do nothing if transaction status pending
        } elseif (isset($request['orderStatus']) && $request['orderStatus'] == 'PROCESSING') {
            exit();
            // without any reason declined
        } else {
            $input['status'] = '0';
            $input['reason'] = 'Transaction declined.';

            // store transaction
            $this->Transaction->storeData($input, $input['is_request_from_vt']);
        }

        // update transaction_session record
        try {
            DB::table('transaction_session')
                ->where('transaction_id', $request['referenceId'])
                ->update([
                    'is_completed' => '1'
                ]);
        } catch (\Exception $e) {
            // 
        }

        exit();
    }

    // ================================================
    /* method : redirect
    * @param  : 
    * @description : redirect only to send in
    */ // ==============================================
    public function redirect(Request $request, $session_id)
    {
        return $request->all();
    }

    // ================================================
    /* method : cancel
    * @param  : 
    * @description : cancel from otp form
    */ // ==============================================
    public function cancel($session_id)
    {
        // get $input data
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }

        $input = json_decode($input_json['request_data'], true);

        $input['status'] = '0';
        $input['reason'] = 'Transaction aborted in OTP page.';

        // redirect back to $response_url
        $transaction_response = $this->storeTransaction($input);
        // unset($input['api_key']);
        // $this->Transaction->storeData($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    // ================================================
    /*  method : curlPost
    * @ param  : getStatusAgain
    * @ Description : curl post response
    */ // ==============================================
    public function curlPost($url, $data, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_body = curl_exec($ch);
        curl_close($ch);
        \Log::info([
            'wyre_post' => $response_body,
        ]);
        return $response_body;
    }

    // ================================================
    /*  method : curlGet
    * @ param  : 
    * @ Description : curl get response
    */ // ==============================================
    public function curlGet($url, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response_body = curl_exec($ch);

        curl_close($ch);

        return $response_body;
    }

    public function curlStatusGet($url, $headers, $count)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response_body = curl_exec($ch);

        curl_close($ch);

        $response = json_decode($response_body, true);

        if (isset($response['status']) && $response['status'] == 'RUNNING_CHECKS' && $count <= 7) {
            \Log::info(['status_response_again' => $response, 'count' => $count]);
            $count = $count + 1;
            return $this->curlStatusGet($url, $headers, $count);
        } else {
            return [
                'response' => json_decode($response_body, true),
                'count' => $count
            ];
        }
    }
}
