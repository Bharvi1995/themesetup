<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\Transaction;
use App\TransactionSession;

class Aron3ds extends Controller
{
    const BASE_URL = 'https://api.aronhub.com/api/KMPI';

    use StoreTransaction;

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
        $payload = [
            'sid' => $check_assign_mid->mid,
            'cardNo' => $input['card_no'],
            'purchAmount' => $input["converted_amount"],
            'expiry' => $input['ccExpiryMonth']."".substr($input['ccExpiryYear'], -2),
            'userId' => (string) \Str::uuid(),
            'name' => $input['first_name'] . " " . $input['last_name'],
            'url' => 'https://testpay.com',
            'transID' => $input['session_id'],
            'cardBrand' => 'Visa'
        ];

        $request_url = self::BASE_URL."/start.asp";

        $payment_response = $this->curlPostRequest($request_url, $payload);
        $response = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $payment_response), true);

        \Log::info([
            'aron-response' => $payment_response
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

    public function redirect(Request $request, $session_id)
    {
        $request_data = $request->all();
        \Log::info([
            'fcfpay_redirect_data' => $request_data
        ]);
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);
        $data = [
            "sid" => $check_assign_mid->mid,
            "transID" => $session_id
        ];
        

        $request_url = self::BASE_URL . '/status.asp';
        $response_data = $this->curlPostRequest($request_url, $data);
        \Log::info([
            'response-data' => $response_data,
        ]);
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
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
