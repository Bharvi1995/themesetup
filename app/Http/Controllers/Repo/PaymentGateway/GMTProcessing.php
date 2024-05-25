<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\StoreTransaction;
use App\TransactionSession;

class GMTProcessing extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://cc.gmtprocessing.com/rest/process';

    public function checkout($input, $check_assign_mid)
    {
        $data = [
            'action' => 'create',
            'amount' => $input["converted_amount"],
            'order_id' => $input['session_id'],
            'currency' => $input["converted_currency"],
            'cardNumber' => $input['card_no'],
            'expMonth' => $input['ccExpiryMonth'],
            'expYear' => $input['ccExpiryYear'],
            'cvv' => $input['cvvNumber'],
            'firstName' => $input['first_name'],
            'lastName' => $input['last_name'],
            'ipAddress' => $input['ip_address'],
            'email' => $input['email'],
            'cardType' => 'visa',
            'phone' => $input['phone_no'],
            'billingCountry' => $input['country'],
            'billingState' => $input['state'],
            'billingCity' => $input['city'],
            'billingAddress' => $input['address'],
            'billingPostCode' => $input['zip']
        ];

        $request_url = self::BASE_URL . '/' . $check_assign_mid->api_key;

        $payment_response = $this->curlPostRequest($request_url, $data);

        \Log::info([
            'gmt-processing-response' => $payment_response
        ]);

        if (isset($payment_response)) {
            if (isset($payment_response['data']) && isset($payment_response['data']['id'])) {
                $input['gateway_id'] = $payment_response['data']['id'] ?? null;
                $this->updateGatewayResponseData($input, $payment_response);
            }

            if (isset($payment_response['data']) && $payment_response['data']['status'] == 'APPROVED') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was processed successfully.';
                $input['descriptor'] = $check_assign_mid->descriptor;
            } else if (isset($payment_response['data']) && $payment_response['data']['status'] == 'PROCESSING - PENDING VERIFICATION') {
                $input['status'] = '7';
                $input['reason'] = '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.';
                $input['redirect_3ds_url'] = $payment_response['data']['url'];
            } else {
                $input['status'] = '0';
                $input['reason'] = $payment_response['data']['message'] ? $payment_response['data']['message'] : 'Transaction declined.';
            }
        } else {
            $input['status'] = '0';
            $input['reason'] = 'Transaction declined.';
        }

        return $input;
    }

    public function curlPostRequest($url, $data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }
}
