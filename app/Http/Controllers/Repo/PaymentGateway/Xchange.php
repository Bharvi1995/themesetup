<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\StoreTransaction;
use App\TransactionSession;
use App\Transaction;

class Xchange extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://gw.xchangefinance.uk';

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
            'request' => array(
                'tracking_id' => $input['session_id'],
                'currency' => $input["converted_currency"],
                'amount' => floatval($input["converted_amount"]) * 100,
                "language" => "en",
                "test" => false,
                "description" => $input['session_id'],
                "credit_card" => array(
                    'number' => $input['card_no'],
                    'exp_month' => $input['ccExpiryMonth'],
                    'exp_year' => $input['ccExpiryYear'],
                    'verification_value' => $input['cvvNumber'],
                    'holder' => $input['first_name'] . " " . $input['last_name']
                ),
                "billing_address" => array(
                    'first_name' => $input['first_name'],
                    'last_name' => $input['last_name'],
                    'country' => $input['country'],
                    'state' => $input['state'],
                    'city' => $input['city'],
                    'address' => $input['address'],
                    'zip' => $input['zip'],
                ),
                "customer" => array(
                    'ip' => $input['ip_address'],
                    'email' => $input['email']
                ),
                'return_url' => route('xchange.return', $input["session_id"]),
                'notification_url' => route('xchange.notify', $input["session_id"])
            )
        ];

        foreach ($data as $k => $a) {
            $data[$k] = json_decode(json_encode($a));
        }

        $request_url = self::BASE_URL . '/transactions/payments';

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($check_assign_mid->mid . ':' . $check_assign_mid->secret_key)
        );

        $response = $this->curlPostRequest($request_url, $data, $headers);

        \Log::info([
            'xchange-response' => $response
        ]);

        if (isset($response) && isset($response['transaction'])) {
            if (isset($response['transaction']['uid'])) {
                $input['gateway_id'] = $response['transaction']['uid'] ?? null;
                $this->updateGatewayResponseData($input, $response);
            }

            if ($response['transaction']['status'] == 'successful') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was processed successfully.';
                $input['descriptor'] = $check_assign_mid->descriptor;
            } else if ($response['transaction']['status'] == 'incomplete' && isset($response['transaction']['redirect_url'])) {
                $input['status'] = '7';
                $input['reason'] = '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.';
                $input['redirect_3ds_url'] = $response['transaction']['redirect_url'];
            } else {
                $input['status'] = '0';
                $input['reason'] = $response['transaction']['message'] ? $response['transaction']['message'] : 'Transaction declined.';
            }
        } else {
            $input['status'] = '0';
            $input['reason'] = 'Your transaction could not processed.';
        }

        return $input;
    }

    public function return(Request $request, $session_id)
    {
        $response = $request->all();
        \Log::info([
            'xchange-return' => $response,
            'id' => $session_id
        ]);
        if (!empty($session_id)) {
            $transaction_session = DB::table('transaction_session')
                ->where('transaction_id', $session_id)
                ->first();
            if ($transaction_session == null) {
                $error = 'Transaction not found.';
            }
            $input = json_decode($transaction_session->request_data, 1);
            $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);

            $headers = array(
                'Authorization: Basic ' . base64_encode($check_assign_mid->mid . ':' . $check_assign_mid->secret_key)
            );
            
            $request_url = self::BASE_URL . '/transactions/' . $request->get('uid');
            $response = $this->curlGetRequest($request_url, $headers);
            $arrResponse = json_decode($response, true);
            
            \Log::info([
                'xchange-status-response' => $arrResponse,
            ]);

            if ($arrResponse['transaction']['status'] == "failed" || $arrResponse['transaction']['status'] == "expired") {
                $input['status'] = '0';
                $input['reason'] = (isset($arrResponse['transaction']['message']) ? $arrResponse['transaction']['message'] : 'Your transaction could not processed.');
            } else if ($arrResponse['transaction']['status'] == 'successful') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
            } else {
                $input['status'] = '2';
                $input['reason'] = 'Transaction is in pending';
            }
            unset($input["reqest_data"]);
            $input['gateway_id'] = $arrResponse['transaction']['uid'] ?? null;
            $this->updateGatewayResponseData($input, $response);
            // store transaction
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        }
    }

    public function notify(Request $request, $session_id)
    {
        $response = $request->all();
        \Log::info([
            'xchange-notify' => $response,
            'id' => $session_id
        ]);
        if (!empty($session_id)) {
            $transaction_session = DB::table('transaction_session')
                ->where('transaction_id', $session_id)
                ->first();
            if ($transaction_session == null) {
                $error = 'Transaction not found.';
            }
            $input = json_decode($transaction_session->request_data, 1);
            if ($response['transaction']['status'] == "failed" || $response['transaction']['status'] == "expired") {
                $input['status'] = '0';
                $input['reason'] = (isset($response['transaction']['message']) ? $response['transaction']['message'] : 'Your transaction could not processed.');
            } else if ($response['transaction']['status'] == 'successful') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
            } else {
                $input['status'] = '2';
                $input['reason'] = 'Transaction is in pending';
            }
            unset($input["request_data"]);
            // store transaction
            $transaction_response = $this->storeTransaction($input);
            exit("notify");
        }
    }

    public function curlPostRequest($url, $data, $headers)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            $headers
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }

    public function curlGetRequest($url, $headers)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => $headers
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return $response;
    }
}
