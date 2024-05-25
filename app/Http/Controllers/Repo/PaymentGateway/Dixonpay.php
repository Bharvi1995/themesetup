<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Session;
use App\Transaction;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\StoreTransaction;

class Dixonpay extends Controller
{
    // const BASE_URL = 'https://secure.dixonpay.com/test/payment'; // test
    const BASE_URL = 'https://secure.dixonpay.com/payment'; // live

    use StoreTransaction;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->Transaction = new Transaction;
    }

    // ================================================
    /* method : transaction
     * @param  : 
     * @Description : transaction method
     */ // ==============================================
    public function checkout($input, $check_assign_mid)
    {
        $input['converted_amount'] = number_format((float)$input['converted_amount'], 2, '.', '');

        $hash_data = $check_assign_mid->mid . $check_assign_mid->terminal_no . $input['session_id'] . $input['converted_currency'] . $input['converted_amount'] . $input['card_no'] . $input['ccExpiryYear'] . $input['ccExpiryMonth'] . $input['cvvNumber'] . $check_assign_mid->key;
        $signature = hash('sha256', trim($hash_data));

        $payload = [
            'merNo' => $check_assign_mid->mid,
            'terminalNo' => $check_assign_mid->terminal_no,
            'orderNo' => $input['session_id'],
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
            'phone' => $input['phone_no'],
            'country' => $input['country'],
            'state' => $input['state'],
            'city' => $input['city'],
            'address' => $input['address'],
            'zip' => $input['zip'],
            'encryption' => $signature,
            'webSite' => $check_assign_mid->website,
            'uniqueId' => (string) \Str::uuid(),
        ];

        $request_payload = http_build_query($payload);

        $request_url = self::BASE_URL;

        $response = $this->curlPostRequest($request_url, $request_payload);
        $xml = simplexml_load_string($response);
        $json = json_encode($xml);
        $response_data = json_decode($json, true);

        // \Log::info([
        //     'dixonpay-response' => $response_data
        // ]);

        if (isset($response_data)) {
            $input['gateway_id'] = $response_data['tradeNo'] ?? null;
            $this->updateGatewayResponseData($input, $response);

            if ($response_data['orderStatus'] == '1') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was processed successfully.';
                $input['descriptor'] = $check_assign_mid->descriptor;
            } else {
                if (isset($response_data['orderInfo']) && $response_data['orderInfo'] == 'ENCRYTION ERROR') {
                    \Log::info([
                        'DixonpayEncryptionError' => [
                            'hash_data' => $hash_data,
                            'signature' => $signature,
                            'request_data' => $payload
                        ]
                    ]);
                }

                $input['status'] = '0';
                $input['reason'] = $response_data['orderInfo'] ? $response_data['orderInfo'] : 'Transaction declined.';
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
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
