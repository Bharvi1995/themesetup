<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\StoreTransaction;
use App\TransactionSession;

class EastPayment extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://pay.eastpayment.net/interface';

    public function checkout($input, $check_assign_mid)
    {
        $signSrc = $check_assign_mid->mid . $check_assign_mid->gateway . $input['order_id'] . $input["converted_currency"] . $input["converted_amount"] . $input['first_name'] . $input['last_name'] . $input['card_no'] . $input['ccExpiryYear'] . $input['ccExpiryMonth'] . $input['cvvNumber'] . $input['email'] . $check_assign_mid->key;

        $signInfo = hash('sha256', trim($signSrc));

        $data = [
            'merNo' => $check_assign_mid->mid,
            'gatewayNo' => $check_assign_mid->gateway,
            'orderNo' => $input['order_id'],
            'orderCurrency' => $input["converted_currency"],
            'orderAmount' => $input["converted_amount"],
            'cardNo' => $input['card_no'],
            'cardExpireMonth' => $input['ccExpiryMonth'],
            'cardExpireYear' => $input['ccExpiryYear'],
            'cardSecurityCode' => $input['cvvNumber'],
            'firstName' => $input['first_name'],
            'lastName' => $input['last_name'],
            'issuingBank' => 'Bank of china',
            'ip' => $input['ip_address'],
            'email' => $input['email'],
            'PaymentMethod' => 'Credit Card',
            'webSite' => $check_assign_mid->website,
            'phone' => $input['phone_no'],
            'country' => $input['country'],
            'state' => $input['state'],
            'city' => $input['city'],
            'address' => $input['address'],
            'zip' => $input['zip'],
            'signInfo' => $signInfo,
        ];

        $request_url = self::BASE_URL . '/WSTestTPInterface';  // testing
//         $request_url = self::BASE_URL . '/WSTPInterface';  // live

        $request_payload = http_build_query($data, '', '&');

        $response = $this->curlPostRequest($request_url, $request_payload);
        $xml = simplexml_load_string($response);
        $json = json_encode($xml);
        $payment_response = json_decode($json, true);

        if ($payment_response['orderStatus'] == '1') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was processed successfully.';
            $input['descriptor'] = $check_assign_mid->descriptor;
        } else {
            $input['status'] = '0';
            $input['reason'] = $payment_response['orderInfo'] ? $payment_response['orderInfo'] : 'Transaction declined.';
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
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
