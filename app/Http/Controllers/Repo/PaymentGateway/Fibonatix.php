<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\Transaction;
use App\TransactionSession;

class Fibonatix extends Controller
{
    const BASE_URL = 'https://channel.paragon.online';

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
        $controlSrc = $check_assign_mid->endpoint . $input["session_id"] . intVal($input["converted_amount"] * 100) . $input["email"] . $check_assign_mid->control;
        $control = sha1($controlSrc);

        $payload = [
            'client_orderid' => $input["session_id"],
            'amount' => $input["converted_amount"],
            'currency' => $input["converted_currency"],
            'first_name' => $input["first_name"],
            'last_name' => $input["last_name"],
            'country' => $input["country"],
            'email' => $input["email"],
            'ipaddress' => $input["ip_address"],
            'credit_card_number' => $input["card_no"],
            'expire_month' => $input["ccExpiryMonth"],
            'expire_year' => $input["ccExpiryYear"],
            'cvv2' => $input["cvvNumber"],
            'card_printed_name' => $input["first_name"] . " " . $input["last_name"],
            'redirect_url' => route("fibonatix.redirect", $input["session_id"]),
            'server_callback_url' => route("fibonatix.callback", $input["session_id"]),
            'control' => $control
        ];

        \Log::info([
            'request' => $payload,
        ]);

        $request_url = self::BASE_URL . '/v2/sale-form/' . $check_assign_mid->endpoint;
        $response = $this->curlPostRequest($request_url, $payload);
        $responseData = array();
        parse_str($response, $responseData);
        foreach ($responseData as $key => $value) {
            $responseData[$key] = trim(preg_replace('/\s\s+/', ' ', $value));
        }

        \Log::info([
            'response' => $responseData,
        ]);

        if (isset($responseData)) {
            if (isset($responseData["serial-number"])) {
                $input['gateway_id'] = $responseData["serial-number"] ?? "";
                $this->updateGatewayResponseData($input, $responseData);
            }
            if ($responseData["type"] == "async-form-response") {
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                    'redirect_3ds_url' => $responseData["redirect-url"],
                ];
            } else {
                $input['status'] = '0';
                $input['reason'] = (isset($responseData["error-message"]) && !empty($responseData["error-message"]) ? $responseData["error-message"] : 'Your transaction could not processed.');
            }
        } else {
            $input['status'] = '0';
            $input['reason'] = 'Your transaction could not processed.';
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

        $controlSrc = $check_assign_mid->login . $sessionId . $paymentResponse['paynet-order-id'] . $check_assign_mid->control;
        $control = sha1($controlSrc);

        $payload = array(
            'client_orderid' => $sessionId,
            'orderid' => $paymentResponse['paynet-order-id'],
            'by-request-sn' => $paymentResponse['serial-number'],
            'control' =>  $control
        );

        $request_url = self::BASE_URL . '/v1/status/' . $check_assign_mid->endpoint;
        $response = $this->curlPostRequest($request_url, $payload);
        $responseData = array();
        parse_str($response, $responseData);
        foreach ($responseData as $key => $value) {
            $responseData[$key] = trim(preg_replace('/\s\s+/', ' ', $value));
        }

        \Log::info([
            'fibonatix-status-response' => $responseData
        ]);

        if ($responseData["type"] == "status-response" && $responseData["status"] == "approved") {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was processed successfully.';
        } else if ($responseData["status"] == "pending") {
            $input['status'] = '2';
            $input['reason'] = 'Transaction is pending in acquirer system, please check after few minutes.';
        } else {
            $input['status'] = '0';
            $input['reason'] = (isset($responseData["error-message"]) && !empty($responseData["error-message"]) ? $responseData["error-message"] : 'Your transaction could not processed.');
        }
        
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function callback(Request $request, $id)
    {
        \Log::info([
            'fibonatix-callback_response' => $request->all(),
            'id' => $id
        ]);

        $responseData = $request->all();
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $id)
            ->first();

        if ($data == null) {
            return abort(404);
        }
        sleep(5);
        $input = json_decode($data->request_data, 1);
        if ($responseData["type"] == "status-response" && $responseData["status"] == "approved") {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was processed successfully.';
        } else {
            $input['status'] = '0';
            $input['reason'] = (isset($responseData["error-message"]) && !empty($responseData["error-message"]) ? $responseData["error-message"] : 'Your transaction could not processed.');
        }
        $transaction_response = $this->storeTransaction($input);
        exit();
    }

    public function curlPostRequest($url, $data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        \Log::info([
            'curl-response' => $response,
        ]);
        return $response;
    }
}
