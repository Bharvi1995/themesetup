<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Storage;
use \stdClass;
use App\User;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use App\TransactionSession;
use App\Transaction;
use Illuminate\Http\Request;

class Royalpay extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://aliumpay.com/api';

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
        $payload = new \stdClass();
        $url = new \stdClass();
        $system_fields = new \stdClass();
        $url->callback_url = route('royalpay-callback', $input["session_id"]);
        $url->fail_url = route('royalpay-fail', $input["session_id"]);
        $url->pending_url = route('royalpay-pending', $input["session_id"]);
        $url->success_url = route('royalpay-success', $input["session_id"]);
        $system_fields->card_number = $input['card_no'];
        $system_fields->card_month = $input['ccExpiryMonth'];
        $system_fields->card_year = $input['ccExpiryYear'];
        $system_fields->cardholder_name = $input['first_name']." ".$input['last_name'];
        $system_fields->card_cvv = $input['cvvNumber'];
        $system_fields->client_id = (string) \Str::uuid();
        $payload->transaction_id = $input['session_id'];
        $payload->amount = strval($input["converted_amount"]);
        $payload->currency = $input["converted_currency"];
        $payload->payment_system = "CardGate";
        $payload->url = $url;
        $payload->system_fields = $system_fields;
        $sign = md5(json_encode($payload) . $check_assign_mid->secret_key);

        $headers = array(
            'Content-Type: application/json',
            'Auth: ' . $check_assign_mid->auth,
            'Sign: ' . $sign
        );

        $request_url = self::BASE_URL . '/deposit/create';
        $response = $this->curlPostRequest($request_url, $payload, $headers);
        $payment_response = json_decode($response, true);
        \Log::info([
            'royalpay-deposit-create' => $response,
        ]);

        if ($response) {
            $input['gateway_id'] = $payment_response['id'];
            $this->updateGatewayResponseData($input, $payment_response);
            if ($payment_response['status'] === 'created') {
                $input['status'] = '7';
                $input['reason'] = '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.';
                $input['redirect_3ds_url'] = $payment_response['redirect']['url'] . '?' . key((array)$payment_response['redirect']['params']) . '=' . $payment_response['redirect']['params'][key((array)$payment_response['redirect']['params'])];
            } else {
                $input['status'] = '0';
                $input['reason'] =  'Transaction declined.';
            }
        } else {
            $input['status'] = '0';
            $input['reason'] =  'Transaction declined.';
        }

        return $input;
    }

    public function callback(Request $request, $session_id)
    {
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();
        if ($input_json == null) {
            return abort(404);
        }
        $request_data = $request->all();
        \Log::info([
            'royalpay-callback' => $request_data
        ]);
        $input = json_decode($input_json['request_data'], true);
        if (!empty($request_data['status'])) {
            $input['status'] = '2';
            $input['reason'] = 'Your transaction is in Pending.';
            if (!empty($request_data['status']) && $request_data['status'] == 'ok') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was processed successfully.';
            } else if (!empty($request_data['status']) && $request_data['status'] == 'error') {
                $input['status'] = '0';
                $input['reason'] = 'Your transaction was Declined.';
            } else if (!empty($request_data['status']) && $request_data['status'] == "cancel") {
                $input['status'] = '0';
                $input['reason'] = 'Your transaction was Canceled.';
            }
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        }
    }

    public function success(Request $request, $session_id)
    {
        $request_data = $request->all();
        \Log::info([
            'royalpay-success' => $request_data,
        ]);
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $arrResponse = json_decode($input_json["response_data"]);
        $input = json_decode($input_json['request_data'], true);
        $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);
        $input['status'] = '1';
        $input['reason'] = 'Your transaction has been processed successfully.';
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function fail(Request $request, $session_id)
    {
        $request_data = $request->all();
        \Log::info([
            'royalpay-fail' => $request_data,
        ]);
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $arrResponse = json_decode($input_json["response_data"]);
        $input = json_decode($input_json['request_data'], true);
        $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);
        $input['status'] = '0';
        $input['reason'] = 'Your transaction could not processed.';
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function pending(Request $request, $session_id)
    {
        $request_data = $request->all();
        \Log::info([
            'royalpay-pending' => $request_data,
        ]);
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $arrResponse = json_decode($input_json["response_data"]);
        $input = json_decode($input_json['request_data'], true);
        $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);
        $input['status'] = '2';
        $input['reason'] = 'Transaction is in pending.';
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
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
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        \Log::info([
            'curl-response' => $response,
        ]);
        return $response;
    }
}
