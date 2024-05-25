<?php

namespace App\PaymentGateways;

use App\TransactionSession;

class WonderlandPaymentGateway extends PaymentGateway implements PaymentGatewayContract
{
    const BASE_URL = 'https://pay.wonderlandpay.com/TPInterface'; // test
    // const BASE_URL = 'https://pay.wonderlandpay.com/TestTPInterface'; // test

    public function charge()
    {
        return __CLASS__;
    }

    public function transaction($input, $check_assign_mid)
    {
        $input['converted_amount'] = number_format((float)$input['converted_amount'], 2, '.', '');

        $signSrc = $check_assign_mid->mid_no . $check_assign_mid->gateway_no . $input['order_id'] . $input["converted_currency"] . $input["converted_amount"] . $input["card_no"] . $input['ccExpiryYear'] . $input['ccExpiryMonth'] . $input["cvvNumber"] . $check_assign_mid->key;

        $signInfo = hash('sha256', trim($signSrc));

        $data = [
            'merNo' => $check_assign_mid->mid_no,
            'gatewayNo' => $check_assign_mid->gateway_no,
            'orderNo' => $input['order_id'],
            'orderCurrency' => $input['converted_currency'],
            'orderAmount' => $input['converted_amount'],
            'cardNo' => $input['card_no'],
            'cardExpireMonth' => $input['ccExpiryMonth'],
            'cardExpireYear' => $input['ccExpiryYear'],
            'cardSecurityCode' => $input['cvvNumber'],
            'firstName' => $input['first_name'],
            'lastName' => $input['last_name'],
            'email' => $input['email'],
            'ip' => $input['ip_address'],
            'webSite' => $check_assign_mid->website,
            'phone' => $input['phone_no'],
            'country' => $input['country'],
            'state' => $input['state'],
            'city' => $input['city'],
            'address' => $input['address'],
            'zip' => $input['zip'],
            'uniqueId' => (string)\Str::uuid(),
            'signInfo' => $signInfo,
        ];

        $request_data = http_build_query($data, '', '&');

        $request_url = self::BASE_URL;

        // removed curl call from here and added this function
        $result = curlPost($request_url, $request_data);

        // response from wonderland
        $xml = simplexml_load_string($result);
        $json = json_encode($xml);
        $array = json_decode($json, true);

        try {
            $input['gateway_id'] = $array['tradeNo'] ?? null;

            // update transaction_session record
            $session_update_data = TransactionSession::where('transaction_id', $input['transaction_id'])
                ->first();

            $session_request_data = json_decode($session_update_data->request_data, 1);
            $session_request_data['gateway_id'] = $input['gateway_id'];

            $session_update_data->update([
                'request_data' => json_encode($session_request_data),
                'gateway_id' => $input['gateway_id'],
                'response_data' => $json
            ]);
        } catch (\Exception $e) {
            Log::info('', ['dixonpay_session_update' => $e->getMessage()]);
        }

        if ($array['orderStatus'] == '1') {
            $return_data['status'] = '1';
            $return_data['reason'] = 'Your transaction was processed successfully.';
        } else {
            $return_data['status'] = '0';
            $return_data['reason'] = $array['orderInfo'] ? $array['orderInfo'] : 'Transaction declined.';
        }

        return $return_data;
    }
}
