<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\Transaction;
use App\TransactionSession;

class AMLNode extends Controller
{

    const BASE_URL = 'https://api.amlnode.com'; //Live

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
        $payload = new \stdClass();
        $attributes = new \stdClass();
        $attributes->reference_id = $input['session_id'];
        $attributes->amount = $input["converted_amount"];
        $attributes->currency = $input["converted_currency"];
        $attributes->service_id = 'btc';
        $attributes->callback_url = route("amlnode.callback", $input["session_id"]);
        $payload->transaction_type = "payment";
        $payload->attributes = $attributes;

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($check_assign_mid->mid . ':' . $check_assign_mid->api_key)
        );

        $request_url = self::BASE_URL . '/psp/v1/orders';
        $response_data = $this->curlPostRequest($request_url, $payload, $headers);
        \Log::info([
            'response' => $response_data,
        ]);
        if (isset($response_data)) {
            $input['gateway_id'] = $response_data->attributes->id;
            $this->updateGatewayResponseData($input, $response_data);
            if ($response_data->attributes->status === 'created') {
                $input['status'] = '7';
                $input['reason'] = '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.';
                $input['redirect_3ds_url'] = route('amlnode.initialize', $input["session_id"]);
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
        $gateway_id = $input_json["gateway_id"];
        return view('gateway.amlnode', compact('gateway_id', 'sessionId', 'paymentResponse'));
    }

    public function verify(Request $request)
    {
        $request_data = $request->all();
        \Log::info([
            'amlnode-verify' => $request_data,
        ]);
        $input_json = TransactionSession::where('transaction_id', $request->get('session_id'))
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
            'Authorization: Basic ' . base64_encode($check_assign_mid->mid . ':' . $check_assign_mid->api_key)
        );
        $request_url = self::BASE_URL . '/psp/v1/orders/' . $request->get('order_id');
        $response = $this->curlGetRequest($request_url, $headers);
        $arrResponse = json_decode($response);
        \Log::info([
            'amlnode-verify-response' => $arrResponse,
        ]);
        if ($arrResponse->attributes && $arrResponse->attributes->status) {
            if ($arrResponse->attributes->status == "created" || $arrResponse->attributes->status == "processing" || $arrResponse->attributes->status == "processed") {
                if ($arrResponse->attributes->status == "processed") {
                    return response()->json(['status' => 1, "url" => route('amlnode.success', $request->session_id)]);
                } else if ($arrResponse->attributes->status == "processing") {
                    return response()->json(['status' => 2, "url" => route('amlnode.pending', $request->session_id)]);
                } else {
                    return response()->json(['status' => 2, "url" => route('amlnode.pending', $request->session_id)]);
                }
            } else if ($arrResponse->attributes->status == "expired" || $arrResponse->attributes->status == "process_error") {
                $message = isset($arrResponse->attributes->failure_message) ? $arrResponse->attributes->failure_message : 'Your transaction could not processed.';
                $str = $request->session_id . "_" . $message;
                return response()->json(['status' => 0, "url" => route('amlnode.declined', $str)]);
            }
        } else {
            return response()->json(['status' => 2, "url" => route('amlnode.pending', $request->session_id)]);
        }
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
            'Authorization: Basic ' . base64_encode($check_assign_mid->mid . ':' . $check_assign_mid->api_key)
        );
        $request_url = self::BASE_URL . '/psp/v1/orders/' . $input["gateway_id"];
        $response = $this->curlGetRequest($request_url, $headers);
        $arrResponse = json_decode($input_json["response_data"]);

        $arrResponse = json_decode($response);
        if ($arrResponse->attributes) {
            if ($arrResponse->attributes->status == "processed") {
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

    public function declined(Request $request, $session_id)
    {
        $request_data = $request->all();
        \Log::info([
            'amlnode-declined' => $request_data,
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
            'amlnode-success' => $request_data,
        ]);

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
            'amlnode-pending' => $request_data,
        ]);

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

    public function callback(Request $request)
    {
        $request_data = $request->all();
        \Log::info([
            'amlnode-callback-data' => $request_data,
        ]);
        if (isset($request_data) && $request_data["attributes"]["status"] && $request_data["attributes"]["status"] == "processed") {
            $input_json = TransactionSession::where('transaction_id', $request_data["attributes"]["reference_id"])
                ->orderBy('id', 'desc')
                ->first();
            if ($input_json == null) {
                return abort(404);
            }
            $input = json_decode($input_json['request_data'], true);
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
            $transaction_response = $this->storeTransaction($input);
        }
        \Log::info('else');
        exit();
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
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_SLASHES));
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        \Log::info([
            'curl-response' => $response,
        ]);
        return json_decode($response);
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
