<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Mail;
use Session;
use Exception;
use App\User;
use App\Transaction;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Cartalyst\Stripe\Laravel\Facades\Stripe;


class iPayBill extends Controller
{
    use StoreTransaction;

    // const BASE_URL = 'https://secure.ipaybill.com/TestTPInterface'; // Test
    const BASE_URL = 'https://secure.ipaybill.com/TPInterface'; // live
    public function __construct() {
        $this->user = new User;
        $this->Transaction = new Transaction;
    }
    // ================================================
    /* method : stripeForm
    * @param  :
    * @Description : Load stripe test form
    */// ==============================================
    public function checkout($input, $check_assign_mid)
    {
        // create payment method
        $payment_url = self::BASE_URL;
        $signSrc = $check_assign_mid->mer_no.$check_assign_mid->gateway_no.$input['order_id'].$input["converted_currency"].$input["converted_amount"].$input["card_no"].$input['ccExpiryYear'].$input['ccExpiryMonth'].$input["cvvNumber"].$check_assign_mid->key;
        $signInfo = hash('sha256', trim($signSrc));
        $payment_data = [
            "merNo" => $check_assign_mid->mer_no,
            "gatewayNo" => $check_assign_mid->gateway_no,
            "orderNo" => $input['order_id'],
            "orderCurrency" => $input["converted_currency"],
            "orderAmount" => $input["converted_amount"],
            "shipFee" => "0.00",
            "cardNo" => $input["card_no"],
            "cardExpireMonth" => $input['ccExpiryMonth'],
            "cardExpireYear" => $input['ccExpiryYear'],
            'cardSecurityCode' =>$input["cvvNumber"],
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
            'signInfo' => $signInfo
        ];
        $curl = curl_init(); 
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_URL, $payment_url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payment_data, '', '&'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        $xml = simplexml_load_string($result);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        $orderNo = $input['order_id'];
        if(isset($input['uniqueId']) || isset($input['deviceNo'])) {
            unset($input['uniqueId']);
            unset($input['deviceNo']);
        }
        unset($input["api_key"]);

        // \Log::info([
        //     'iPayBill_Response' => $array
        // ]);

        if($array['orderStatus'] == '0') {
            $input['order_id'] = $orderNo;
            $input['status'] = '0';
            $input['reason'] = $array['orderInfo'];
            //$this->Transaction->storeData($input);
            $reason = $input['reason'];
            return ['status' => '0', 'reason' =>$reason, 'order_id' => $orderNo];
        } else {
            $input['order_id'] = $orderNo;

            if($array['orderStatus'] == '2') {
                $input['status'] = '4';
                $input['reason'] = $array['orderInfo'];
                $message = 'Your Transaction Is To Be Confirmed!!';
            }
            elseif ($array['orderStatus'] == '-1') {
                $input['status'] = '2';
                $input['reason'] = $array['orderInfo'];
                $message = 'Your Transaction Is Pending!!';
            }
            else {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was processed successfully.';
                $message = 'Your transaction was processed successfully.';
            }
            //$this->Transaction->storeData($input);
            return ['status' => $input['status'], 'reason' => $message, 'order_id' => $orderNo];
        }
    }
}
