<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\User;
use App\Transaction;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use App\TransactionSession;
use Illuminate\Support\Facades\Http;
use Log;

class Kpentag extends Controller
{
    use StoreTransaction;

    // const BASE_URL = 'https://test.kpentagpaymentz.com'; // test
    const BASE_URL = 'https://prod.kpentagpaymentz.com'; // test

    public function checkout($input, $check_assign_mid)
    {
        $request_data = [
            'amount' => $input['converted_amount'],
            'external_transaction_ref' => $input['session_id'],
            'api_prefix' => $check_assign_mid->apikey,
            // 'api_prefix' => "W52vlI7K",
            'currency' => $input['converted_currency'],
            'card_number' => $input['user_card_no'],
            'cvv' => $input['user_cvv_number'],
            'expiry_month' => $input['user_ccexpiry_month'],
            'expiry_year' => str_replace("20", "", $input['user_ccexpiry_year']),
            'source_phone' => $input['user_phone_no'],
            'card_holder_name' => $input['user_first_name'] . ' ' . $input['user_last_name'],
            'debit_callback_url' => route('kpentag.webhook', $input['session_id']),
            // 'debit_callback_url' => "https://webhook.site/60aafdff-eeeb-46e8-ad83-f989ca7de764",
            'email' => $input['user_email'],
            'redirect_url' => route('kpentag.redirect', $input['session_id']),
            // 'redirect_url' => "https://webhook.site/60aafdff-eeeb-46e8-ad83-f989ca7de764",
        ];

        $response = Http::withHeaders(["Content-Type" => "application/json", "Accept" => "application/json"])->post(self::BASE_URL . "/client/transaction/card-debit/", $request_data)->json();

        Log::info(["payload" => $request_data, "kpentag-res" => $response]);

        // store mid payload
        $request_data['card_number'] = cardMasking($request_data['card_number']);
        $request_data['cvv'] = 'XXX';
        $this->storeMidPayload($input['session_id'], json_encode($request_data));

        $input['gateway_id'] = $request_data['transaction_id'] ?? '1';
        $this->updateGatewayResponseData($input, $response);

        if (isset($response['status']) && $response['status'] == true && isset($response['transaction_status']) && $response['status'] == 'PENDING'){
            return [
                'status' => '2',
                'reason' => "Your transaction is under process . Please wait for sometime!",
                'order_id' => $input['order_id'],
            ];
        } elseif (isset($response['status']) && $response['status'] == true && isset($response['redirect_url']) && !empty($response['redirect_url'])) {
            return [
                'status' => '7',
                'reason' => "Please redirect to the specified 'payment_link' to complete the transaction processing.",
                'payment_link' => $response['redirect_url']
            ];
        } elseif (isset($response['status']) && $response['status'] == true && isset($response['data']['status']) && $response['data']['status'] == 'success') {
            return [
                'status' => '1',
                'reason' => "Your payment has been successfully completed.",
                'order_id' => $input['order_id'],
            ];
        } elseif (
            isset($response['status']) && $response['status'] == true &&
            isset($response['data']['status']) && $response['data']['status'] == 'fail'
        ) {
            return [
                'status' => '0',
                'reason' => $response['message'] ?? $response['transaction_message'] ?? $response['exception'] ?? 'The transaction was unsuccessful.',
                'order_id' => $input['order_id'],
            ];
        } else {
            Log::info(['kpentag-response' => $response]);
            return [
                'status' => '0',
                'reason' => $response['message'] ?? $response['transaction_message'] ?? $response['exception'] ?? 'The transaction was unsuccessful.',
                'order_id' => $input['order_id'],
            ];
        }
    }


    public function redirect($id)
    {
        $transaction = DB::table('transaction_session')
            ->select("id", "payment_gateway_id", "request_data")
            ->where("transaction_id", $id)
            ->first();

        if (empty($transaction)) {
            abort(404);
        }

        $input = json_decode($transaction->request_data, true);
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

        // call the status API
        $status_data = $this->statusAPI($input, $check_assign_mid);

        // success
        if (
            isset($status_data['status']) && $status_data['status'] == true &&
            isset($status_data['transaction_status']) && $status_data['transaction_status'] == 'SUCCESSFUL'
        ) {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction processed successfully.';
            // pending
        } elseif (
            isset($status_data['status']) && $status_data['status'] == true &&
            isset($status_data['transaction_status']) && $status_data['transaction_status'] == 'PENDING'
        ) {
            $input['status'] = '2';
            $input['reason'] = 'Your transaction is under process . Please wait for sometime!';
            // failed
        } elseif (
            isset($status_data['status']) && $status_data['status'] == true &&
            isset($status_data['transaction_status']) && $status_data['transaction_status'] == 'FAILED'
        ) {
            $input['status'] = '0';
            $input['reason'] = $status_data['message'] ?? $status_data['transaction_message'] ?? $status_data['exception'] ?? 'Transaction failed to pass 3DS.';
        } else {
            \Log::info(['kpentag-return-status' => $status_data]);
            $input['status'] = '0';
            $input['reason'] = $status_data['message'] ?? $status_data['transaction_message'] ?? $status_data['exception'] ?? 'Transaction failed to pass 3DS.';
        }

        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function webhook($id)
    {
        $transaction = DB::table('transaction_session')
            ->select("id", "payment_gateway_id", "request_data")
            ->where("transaction_id", $id)
            ->first();

        if (empty($transaction)) {
            abort(404);
        }

        http_response_code(200);

        $input = json_decode($transaction->request_data, true);
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

        // call the status API
        $status_data = $this->statusAPI($input, $check_assign_mid);

        // success
        if (
            isset($status_data['status']) && $status_data['status'] == true &&
            isset($status_data['transaction_status']) && $status_data['transaction_status'] == 'SUCCESSFUL'
        ) {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction processed successfully.';
            // pending
        } elseif (
            isset($status_data['status']) && $status_data['status'] == true &&
            isset($status_data['transaction_status']) && $status_data['transaction_status'] == 'PENDING'
        ) {
            $input['status'] = '2';
            $input['reason'] = 'Your transaction is under process . Please wait for sometime!';
            // failed
        } elseif (
            isset($status_data['status']) && $status_data['status'] == true &&
            isset($status_data['transaction_status']) && $status_data['transaction_status'] == 'FAILED'
        ) {
            $input['status'] = '0';
            $input['reason'] = $status_data['message'] ?? $status_data['transaction_message'] ?? $status_data['exception'] ?? 'Transaction failed to pass 3DS.';
        } else {
            \Log::info(['kpentag-return-status' => $status_data]);
            $input['status'] = '0';
            $input['reason'] = $status_data['message'] ?? $status_data['transaction_message'] ?? $status_data['exception'] ?? 'Transaction failed to pass 3DS.';
        }

        $this->storeTransaction($input);
        exit();
    }

    public function statusAPI($input, $check_assign_mid)
    {
        $status_url = self::BASE_URL . '/client/transaction/status_check/' . $input['session_id'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $status_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_body = curl_exec($ch);
        curl_close($ch);

        return json_decode($response_body, true);
    }
}
