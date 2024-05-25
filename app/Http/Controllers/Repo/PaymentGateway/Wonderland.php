<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Session;
use App\Transaction;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class Wonderland extends Controller
{
    use StoreTransaction;
    
    const BASE_URL = 'https://pay.wonderlandpay.com/TPInterface'; // live
    // const BASE_URL = 'https://pay.wonderlandpay.com/TestTPInterface'; // test
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->transaction = new Transaction;
    }

    // ================================================
    /* method : transaction
     * @param  : 
     * @Description : wonderland api call
     */// ==============================================
    public function checkout($input, $check_assign_mid)
    {

        try {
            if($input["amount_in_usd"] < 3.5){
                return [
                    'status' => '5',
                    'reason' => 'Amount should be more than 3.5 USD',
                    'order_id' => $input['order_id'],
                ];
            }
            $input['converted_amount'] = number_format((float)$input['converted_amount'], 2, '.', '');
            $signSrc = $check_assign_mid->mid_number.$check_assign_mid->gateway_no.$input['order_id'].$input["converted_currency"].$input["converted_amount"].$input["card_no"].$input['ccExpiryYear'].$input['ccExpiryMonth'].$input["cvvNumber"].$check_assign_mid->key;

            $signInfo = hash('sha256', trim($signSrc));

            $data = [
                'merNo' => $check_assign_mid->mid_number,
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
                'uniqueId' => (string) \Str::uuid(),
                'signInfo' => $signInfo,
            ];
            
            $request_url = self::BASE_URL;
            $result = $this->curlPostRequest($request_url, http_build_query($data, '', '&'));
            // response from wonderland
            $xml = simplexml_load_string($result);
            $json = json_encode($xml);
            $array = json_decode($json, true);

            // \Log::info([
            //         'wonderland-input' => $data
            // ]);
            // \Log::info([
            //         'wonderland-response' => $array
            // ]);

            $input['gateway_id'] = $array['tradeNo'] ?? $input['order_id'];
            $this->updateGatewayResponseData($input, $json);

            if($array['orderStatus'] == '1') {

                return [
                    'status' => '1',
                    'reason' => 'Your transaction has been processed successfully.',
                    'order_id' => $input['order_id']
                ];
            }

            throw new \Exception($array['orderInfo'] ? $array['orderInfo'] : 'Transaction declined.');

        } catch (\Exception $e) {

            // \Log::info([
            //     'wonderland-exception' => $e->getMessage()
            // ]);
            return [
                'status' => '0',
                'reason' => $e->getMessage(), // 'Your transaction could not processed.',
                'order_id' => $input['order_id']
            ];
        }
    }

    public function curlPostRequest($url, $data) {
        if(strstr(strtolower($url), 'https://')) {
            $port = 443;
        }else {
            $port = 80;
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_PORT, $port);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $tmpInfo = curl_exec($curl);
        curl_close($curl);
        return $tmpInfo;
    }
}