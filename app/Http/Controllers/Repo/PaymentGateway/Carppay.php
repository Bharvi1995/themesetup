<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Session;
use App\Transaction;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Rsa;
use App\Traits\StoreTransaction;

class Carppay extends Controller
{
    use StoreTransaction;
    const BASE_URL = 'https://gateway.carppay.com/payment'; // live

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
        $card_type = "";
        if ($input["card_type"] == 2) {
            $card_type = "0";
        } else if ($input["card_type"] == 3) {
            $card_type = "1";
        } elseif ($input["card_type"] == 1) {
            $card_type = "2";
        } else if ($input["card_type"] == 5) {
            $card_type = "3";
        }
        if ($card_type == "") {
            return [
                'status' => '0',
                'reason' => "Your card type is not supported, Please check the card number.",
                'order_id' => $input['order_id']
            ];
        }
        $privateKey = $check_assign_mid->private_key;
        $md5Key = $check_assign_mid->md5_key;
        $request_data = [
            'merId' => $check_assign_mid->mid,
            'orderId' => $input['session_id'],
            'orderAmt' => $input['converted_amount'],
            'channel' => $check_assign_mid->channel,
            'currency' => '1',
            'cardType' => $card_type,
            'cardNo' => $input['card_no'],
            'cardDate' => $input['ccExpiryYear'] . "" . $input['ccExpiryMonth'],
            'cardCvv' => $input['cvvNumber'],
            'cardName' => $input['first_name'] . " " . $input['last_name'],
            'desc' => $input["session_id"],
            'attch' => $input["session_id"],
            'quantity' => 1,
            'price' => $input['converted_amount'],
            'weburl' => "www.google.com",
            'mob' => $input['phone_no'],
            'email' => $input['email'],
            'shipFirstName' => $input['first_name'],
            'shipLastName' => $input['last_name'],
            'shipAddress' => $input["address"],
            'shipCity' => $input["city"],
            'shipState' => $input["state"],
            'shipCountry' => $input["country"],
            'shipZip' => $input["zip"],
            'shipPhone' => $input['phone_no'],
            'shipEmail' => $input['email'],
            'billFirstName' => $input['first_name'],
            'billLastName' => $input['last_name'],
            'billAddress' => $input["address"],
            'billCity' => $input["city"],
            'billState' => $input["state"],
            'billCountry' => $input["country"],
            'billZip' => $input["zip"],
            'billPhone' => $input['phone_no'],
            'billEmail' => $input['email'],
            'java_enabled' => true,
            'color_depth' => '30',
            'screen_height' => '1080',
            'screen_width' => '1920',
            'time_zone_offset' => '-240',
            'accept' => 'application/json',
            'language' => 'en',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip' => $input['ip_address'],
            'notifyUrl' => route('carppay.notify', $input["session_id"]),
            'nonceStr' => (string) \Str::random(20)
        ];
        //echo "<pre>";

        $sign = $this->sign($request_data, $md5Key, $privateKey);
        $request_data["sign"] = $sign;
        //print_r($request_data);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => self::BASE_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($request_data),
            CURLOPT_HTTPHEADER => array(
                'User-Agent: apifox/1.0.0 (https://www.apifox.cn)'
            ),
        ));
        $response = curl_exec($curl);

        $err = curl_error($curl);
        $response_data = json_decode($response);
        \Log::info([
            'carppay-response-data' => $response,
            'carppay-response-data-decoded' => $response_data
        ]);
        if (isset($response_data)) {
            $input['gateway_id'] = $response_data->data->sysorderno ?? null;
            $this->updateGatewayResponseData($input, $response_data);
        }
        if (isset($response_data) && $response_data->data->is_3ds == 1) {
            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                'redirect_3ds_url' => $response_data->data->redirect_url,
            ];
        } else if (isset($response_data) && $response_data->code == "1") {
            return [
                'status' => '1',
                'reason' => 'Your transaction has been processed successfully.',
                'order_id' => $input['order_id']
            ];
        } else {
            return [
                'status' => '0',
                'reason' => (isset($response_data->msg) ? $response_data->msg : 'Your transaction could not processed.'),
                'order_id' => $input['order_id']
            ];
        }
    }

    public function notify(Request $request, $session_id)
    {
        sleep(15);
        $request_data = $request->all();
        http_response_code(200);
        \Log::info([
            'carppay_notify_data' => $request_data
        ]);
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);
        if (isset($request_data['status']) && $request_data['status'] == '1') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
        } else {
            $input['status'] = '2';
            $input['reason'] = 'Transaction is in pending.';
        }
        $transaction_response = $this->storeTransaction($input);
        exit();
    }

    public function sign($data, $md5Key, $privateKey)
    {
        ksort($data);
        reset($data);
        $arg = '';
        foreach ($data as $key => $val) {
            if ($val == '' || $key == 'sign') {
                continue;
            }
            $arg .= ($key . '=' . $val . '&');
        }
        $arg = $arg . 'key=' . $md5Key;
        $sig_data = strtoupper(md5($arg));
        $rsa = new Rsa('', $privateKey);
        return $rsa->sign($sig_data);
    }
}
