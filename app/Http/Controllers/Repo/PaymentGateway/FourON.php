<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\StoreTransaction;
use App\TransactionSession;
use App\Transaction;
use Carbon\Carbon;

class FourON extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://api.4on.me';

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
        $request_method = "POST";
        $request_target = "/?iframe=true";
        $unix_time = time();
        $merchant_id = $check_assign_mid->mid;
        $secret_key = $check_assign_mid->secret_key;
        $content_type = "application/json";

        $request_data = [
            "country" => $input['country'],
            "currency" => $input['converted_currency'],
            "amount" => $input['converted_amount'],
            "user_id" => (string) \Str::uuid(),
            "merchant_id" => "$merchant_id",
            "payment_method_id" => "card",
            "reference_id" => $input['session_id'],
            "first_name" => $input['first_name'],
            "last_name" => $input['last_name'],
            "email" => $input['email'],
            'return_url' => route("FourON.redirect", $input["session_id"]),
            'notification_url' => route("FourON.notify", $input["session_id"]),
        ];

        $request_body = json_encode($request_data);
        $string_to_sign  =  ($request_method . "\n" . $request_target . "\n" . $unix_time . "\n" . $content_type . "\n" . md5($request_body));

        $request_signature = base64_encode(hash_hmac('sha256', $string_to_sign, $secret_key, true));
        $request_url = "https://api.4on.me$request_target";
        $request_headers = [
            "Content-Type: $content_type",
            "Authorization-Timestamp: $unix_time",
            "Authorization: 4on-http-hmac: $merchant_id:$request_signature"
        ];

        $response = $this->curlPostRequest($request_url, $request_body, $request_headers);
        $responseData = json_decode($response);

        \Log::info([
            '4on-response' => $response
        ]);

        if ($response) {
            preg_match('/src="([^"]+)"/', $response, $match);
            if (count($match) > 0) {
                $input['status'] = '7';
                $input['reason'] = '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.';
                $input['redirect_3ds_url'] = $match[1];
            } else {
                $input['status'] = '0';
                $input['reason'] = isset($responseData) ? $responseData->error :  'Transaction declined.';
            }
        } else {
            $input['status'] = '0';
            $input['reason'] = 'Transaction declined.';
        }

        return $input;
    }

    public function redirect($sessionId)
    {
        \Log::info([
            'id' => $sessionId
        ]);

        $data = \DB::table('transaction_session')
            ->where('transaction_id', $sessionId)
            ->first();

        if ($data == null) {
            return abort(404);
        }

        $input = json_decode($data->request_data, 1);
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
        $paymentResponse = json_decode($data->response_data, true);
        $request_method = "GET";
        $request_target = '/payment/' . $sessionId;
        $unix_time = time();
        $merchant_id = $check_assign_mid->mid;
        $secret_key = $check_assign_mid->secret_key;
       
        $string_to_sign  =  ($request_method . "\n" . $request_target . "\n" . $unix_time);

        $request_signature = base64_encode(hash_hmac('sha256', $string_to_sign, $secret_key, true));

        $request_headers = [
            "Authorization-Timestamp: $unix_time",
            "Authorization: 4on-http-hmac: $merchant_id:$request_signature"
        ];

        $request_url = self::BASE_URL . '/payment/' . $sessionId;
        $response = $this->curlGetRequest($request_url, $request_headers);
        
        \Log::info([
            '4on-status-response' => $response
        ]);

        if (isset($response["id"])) {
            $input['gateway_id'] = $response["id"] ?? "";
            $this->updateGatewayResponseData($input, $response);
        }

        if ($response["status"] == "approved") {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was processed successfully.';
        } else if ($response["status"] == "rejected" || $response["status"] == "cancelled") {
            $input['status'] = '0';
            $input['reason'] = (isset($responseData["error"]) && !empty($responseData["error"]) ? $responseData["error"] : 'Your transaction could not processed.');
        } else {
            $input['status'] = '2';
            $input['reason'] = 'Transaction is pending in acquirer system, please check after few minutes.';
        }
        
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function notify(Request $request, $session_id)
    {
        $response = $request->all();
        \Log::info([
            '4on-notify-response' => $response,
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
            if ($response["payload"]["status"] == "rejected" || $response["payload"]["status"] == "cancelled") {
                $input['status'] = '0';
                $input['reason'] = (isset($response["payload"]['error']) ? $response["payload"]['error'] : 'Your transaction could not processed.');
            } else if ($response["payload"]["status"] == "approved") {
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
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            $headers
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
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
        return json_decode($response, true);
    }
}
