<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Storage;
use \stdClass;
use App\User;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use App\TransactionSession;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class Basqet extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://api.basqet.com/v1';

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
        $payload = new \stdClass();
        $customer = new \stdClass();
        $meta = new \stdClass();
        $meta->reference = $input['session_id'];
        $customer->name = $input['first_name'] . " " . $input['last_name'];
        $customer->email = $input['email'];
        $payload->amount = strval($input["converted_amount"]);
        $payload->currency = $input["converted_currency"];
        $payload->customer = $customer;
        $payload->meta = $meta;
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $check_assign_mid->public_key
        );

        $response = $this->initializeTransaction($payload, $headers);
        \Log::info([
            'basqet-initialize' => $response,
        ]);
        if ($response) {
            $payment_response = $this->initiateTransaction($response, $headers);
            \Log::info([
                'basqet-initiate' => $payment_response,
            ]);
            $input['gateway_id'] = $payment_response['data']['id'];
            $this->updateGatewayResponseData($input, $payment_response);
            if ($payment_response['status'] === 'success') {
                $input['status'] = '7';
                $input['reason'] = '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.';
                $input['redirect_3ds_url'] = route('basqet.initialize', $input["session_id"]);
            } else {
                $input['status'] = '0';
                $input['reason'] =  'Transaction declined.';
            }
        } else {
            $input['status'] = '0';
            $input['reason'] =  'Transaction declined.';
        }

        return $input;
    }

    public function initializeTransaction($payload, $headers)
    {
        $request_url = self::BASE_URL . '/transaction';
        $response = $this->curlPostRequest($request_url, $payload, $headers);
        $payment_response = json_decode($response, true);
        if ($payment_response['status'] === 'success') {
            return $payment_response['data'];
        } else {
            return null;
        }
    }

    public function initiateTransaction($payload, $headers)
    {
        $request_url = self::BASE_URL . '/transaction/' . $payload["id"] . '/pay';
        $param["currency_id"] = "4";
        $response = $this->curlPostRequest($request_url, $param, $headers);
        $payment_response = json_decode($response, true);
        return $payment_response;
    }

    public function initialize($sessionId)
    {
        $input_json = TransactionSession::where('transaction_id', $sessionId)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $paymentResponse = json_decode($input_json["response_data"]);
        $input = json_decode($input_json['request_data'], true);
        $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);
        $secret_key = $check_assign_mid->secret_key;
        $gateway_id = $input_json["gateway_id"];
        return view('gateway.basqet', compact('gateway_id', 'sessionId', 'paymentResponse', 'secret_key'));
    }

    public function verify(Request $request)
    {
        $request_data = $request->all();
        \Log::info([
            'basqet-verify' => $request_data,
        ]);
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $request->get('secret_key')
        );
        $request_url = self::BASE_URL . '/transaction/' . $request->get('transaction_id') . '/status';
        $response = $this->curlGetRequest($request_url, $headers);
        $arrResponse = json_decode($response);
        \Log::info([
            'basqet-verify-response' => $arrResponse,
        ]);
        if ($arrResponse->status) {
            if ($arrResponse->status == "success") {
                if ($arrResponse->data->status == "SUCCESSFUL") {
                    return response()->json(['status' => 1, "url" => route('basqet.success', $request->session_id)]);
                } else if ($arrResponse->data->status == "PENDING") {
                    return response()->json(['status' => 2, "url" => route('basqet.pending', $request->session_id)]);
                } else {
                    return response()->json(['status' => 2, "url" => route('basqet.pending', $request->session_id)]);
                }
            } else if ($arrResponse->status == "error") {
                $message = isset($arrResponse->message) ? $arrResponse->message : 'Your transaction could not processed.';
                $str = $request->session_id . "_" . $message;
                return response()->json(['status' => 0, "url" => route('basqet.declined', $str)]);
            }
        } else {
            return response()->json(['status' => 2, "url" => route('basqet.pending', $request->session_id)]);
        }
    }

    public function declined(Request $request, $session_id)
    {
        $request_data = $request->all();
        \Log::info([
            'basqet-redirect' => $request_data,
        ]);
        $arr = explode("_", $session_id);
        $input_json = TransactionSession::where('transaction_id', $arr["0"])
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $arrResponse = json_decode($input_json["response_data"]);
        $input = json_decode($input_json['request_data'], true);
        $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);
        $input['status'] = '0';
        $input['reason'] = (isset($arr["1"]) ? $arr["1"] : 'Your transaction could not processed.');
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function success(Request $request, $session_id)
    {
        $request_data = $request->all();
        \Log::info([
            'basqet-success' => $request_data,
        ]);
        $arr = explode("_", $session_id);
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $arrResponse = json_decode($input_json["response_data"]);
        $input = json_decode($input_json['request_data'], true);
        $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);
        $input['status'] = '1';
        $input['reason'] = 'Your transaction has been processed successfully.';
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function pending(Request $request, $session_id)
    {
        $request_data = $request->all();
        \Log::info([
            'basqet-pending' => $request_data,
        ]);
        $arr = explode("_", $session_id);
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $arrResponse = json_decode($input_json["response_data"]);
        $input = json_decode($input_json['request_data'], true);
        $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);
        $input['status'] = '2';
        $input['reason'] = 'Transaction is in pending.';
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function back($session_id)
    {
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $arrResponse = json_decode($input_json["response_data"]);
        $input = json_decode($input_json['request_data'], true);
        $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $check_assign_mid->secret_key
        );
        $request_url = self::BASE_URL . '/transaction/' . $input_json["gateway_id"] . '/status';
        $response = $this->curlGetRequest($request_url, $headers);
        $arrResponse = json_decode($input_json["response_data"]);
        $input = json_decode($input_json['request_data'], true);
        $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);

        $arrResponse = json_decode($response);
        if ($arrResponse->status) {
            if ($arrResponse->data->status == "successful") {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
            } else {
                $input['status'] = '0';
                $input['reason'] = (isset($arrResponse->message) ? $arrResponse->message : 'Your transaction could not processed.');
            }
        } else {
            $input['status'] = '2';
            $input['reason'] = 'Transaction is in pending.';
        }
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function cancelTransaction(Request $request)
    {
        $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', Crypt::decryptString($request->get('session_id')))
            ->first();
        if ($transaction_session == null) {
            return response()->json(["redirectLink" => null]);
        }
        $input = json_decode($transaction_session->request_data, true);
        $input['status'] = '0';
        $input['reason'] = 'Your transaction could not processed.';
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return response()->json(["redirectLink" => $store_transaction_link]);
    }

    public function paymentReceived(Request $request)
    {
        $request_data = $request->all();
        //echo "<pre>";print_r($request_data);exit();
        \Log::info([
            'basqet-webhook' => $request_data,
        ]);
        if (isset($request_data["data"]["transaction"]["reference"]) && !empty($request_data["data"]["transaction"]["reference"])) {
            $input_json = TransactionSession::where('gateway_id', $request_data["data"]["transaction"]["reference"])
                ->orderBy('id', 'desc')
                ->first();

            if ($input_json == null) {
                return abort(404);
            }
            $input = json_decode($input_json['request_data'], true);
            if ($request_data["data"]["transaction"]["status"] == "SUCCESSFUL") {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
                $transaction_response = $this->storeTransaction($input);
            } else if ($request_data["data"]["transaction"]["status"] == "ABANDONED") {
                $input['status'] = '0';
                $input['reason'] =  isset($request_data["message"]) ? $request_data["message"] : 'Your transaction could not processed.';
                $transaction_response = $this->storeTransaction($input);
            }
            exit();
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
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        \Log::info([
            'curl-response' => $response,
        ]);
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
        return $response;
    }
}
