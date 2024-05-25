<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Log;
use Http;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Oacquiring extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://api.oacquiring.com';
    // const BASE_URL = 'https://testapi.oacquiring.com';

    public function checkout($input, $check_assign_mid)
    {
        if ($input['card_type'] == '2') {
            $terminal_id = $check_assign_mid->visa_terminal_id;
        } else {
            $terminal_id = $check_assign_mid->mc_terminal_id;
        }

        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');

        $auth_url = self::BASE_URL.'/txn/AuthToken';
        $auth_data = [
            'merchant_id' => $check_assign_mid->merchant_id,
            'secret' => $check_assign_mid->secret
        ];
        $auth_json = json_encode($auth_data);
        $this->storeMidPayload($input['session_id'], $auth_json);

        $auth_response = $this->curlRequest($auth_url, $auth_json, [], 'post');

        if (
            isset($auth_response['response_code']) && $auth_response['response_code'] == 'R0006' &&
            isset($auth_response['auth_token']) && !empty($auth_response['auth_token'])
        ) {
            $sale_url = self::BASE_URL.'/txn/sale';
            $sale_headers = [
                'Content-Type: Application/json',
                'Authorization: '.$auth_response['auth_token'],
            ];
            $checksum = hash('sha256', $check_assign_mid->merchant_id.$terminal_id.$input['session_id'].$check_assign_mid->sign_key);
            $sale_data = [
                'merchant_id' => $check_assign_mid->merchant_id,
                'terminal_id' => $terminal_id,
                'order_no' => $input['session_id'],
                'currency' => $input['converted_currency'],
                'amount' => $input['converted_amount'],
                'card_details' => [
                    'card_number' => $input['card_no'],
                    'name' => $input['first_name'].' '.$input['last_name'],
                    'expiry_month' => $input['ccExpiryMonth'],
                    'expiry_year' => $input['ccExpiryYear'],
                    'cvv' => $input['cvvNumber'],
                ],
                'customer_details' => [
                    'first_name' => $input['first_name'],
                    'last_name' => $input['last_name'],
                    'email' => $input['email'],
                    'phone' => $input['phone_no'],
                    'ip_adress' => $input['ip_address'],
                    'address' => $input['address'],
                    'city' => $input['city'],
                    'state' => $input['state'],
                    'country' => $input['country'],
                    'zip' => $input['zip'],
                ],
                'checksum' => $checksum,
                'redirect_url' => route('oacquiring.redirect', $input['session_id']),
                'notification_url' => route('oacquiring.notify', $input['session_id']),
            ];
            $sale_json = json_encode($sale_data);
            $this->storeMidPayload($input['session_id'], $sale_json);

            $sale_response = $this->curlRequest($sale_url, $sale_json, $sale_headers, 'post');

            $input['gateway_id'] = $sale_response['txn_id'] ?? '1';
            $input['descriptor'] = $sale_response['billing_descriptor'] ?? null;
            $this->updateGatewayResponseData($input, $sale_response);

            if (
                isset($sale_response['response_code']) && $sale_response['response_code'] == 'R0100' &&
                isset($sale_response['txn_status']) && $sale_response['txn_status'] == 'SUCCESS'
            ) {
                return [
                    'status' => '1',
                    'reason' => 'Your transaction has been processed successfully.',
                    'order_id' => $input['order_id']
                ];
            } elseif (isset($sale_response['txn_status']) && $sale_response['txn_status'] == 'FAILED') {
                return [
                    'status' => '0',
                    'reason' => $sale_response['txn_status_desc'] ?? 'Could not process the transaction.',
                    'order_id' => $input['order_id'],
                ];
            } else {
                \Log::info(['oacquiring_sale_else' => $sale_response]);
                return [
                    'status' => '0',
                    'reason' => $sale_response['txn_status_desc'] ?? 'Could not process the transaction.',
                    'order_id' => $input['order_id'],
                ];
            }
        } else {
            \Log::info(['oacquiring_auth_else' => $auth_response]);
            return [
                'status' => '0',
                'reason' => $auth_response['txn_status_desc'] ?? 'Could not initialize the transaction.',
                'order_id' => $input['order_id'],
            ];
        }
    }

    public function browser($session_id)
    {
        $transaction_session = DB::table('transaction_session')
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->where('transaction_id', $session_id)
            ->first();

        if (empty($transaction_session)) {
            return abort(404);
        }

        $input = json_decode($transaction_session->request_data, true);
        $response_data = json_decode($transaction_session->response_data, true);
        $action_url = $response_data['redirect']['url'] ?? 'nothing';
        $method = $response_data['redirect']['method'] ?? 'post';
        $parameters = $response_data['redirect']['parameters'] ?? [];

        return view('gateway.oacquiring.browser', compact('action_url', 'method', 'parameters'));
    }

    private function curlRequest($url, $data = null, $headers = [], $method = 'post')
    {
        $ch = curl_init();
        if ($method == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $response = curl_exec($ch);
        curl_close($ch);

        $response_data = json_decode($response, 1);
        return $response_data;
    }
}