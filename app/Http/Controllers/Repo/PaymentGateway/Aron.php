<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\StoreTransaction;
use App\TransactionSession;
use App\Transaction;

class Aron extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://api.aronhub.com/api/kcp/2d_card/card/approval.asp';

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
        $data = [
            'sid' => $check_assign_mid->mid,
            'ordr_idxx' => $input['session_id'],
            'good_mny' => $input["converted_amount"] * 100,
            "good_name" => $input["session_id"] . "#",
            'card_no' => $input['card_no'],
            'expiry_mm' => $input['ccExpiryMonth'],
            'expiry_yy' => substr($input['ccExpiryYear'], -2),
            'card_cvn' => $input['cvvNumber'],
            'buyr_name' => $input['first_name'] . " " . $input['last_name'],
            'buyr_mail' => $input['email']
        ];

        $request_url = self::BASE_URL;

        $payment_response = $this->curlPostRequest($request_url, $data);
        $response = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $payment_response), true);

        \Log::info([
            'aron-response' => $response
        ]);

        if ($response) {
            $input['gateway_id'] = $response['tno'] ?? null;
            $this->updateGatewayResponseData($input, $response);

            if ($response['res_cd'] == '0000') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was processed successfully.';
                $input['descriptor'] = $check_assign_mid->descriptor;
            } else {
                $input['status'] = '0';
                $input['reason'] = $response['res_en_msg'] ? $response['res_en_msg'] : 'Transaction declined.';
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
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
