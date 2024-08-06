<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\StoreTransaction;
use App\TransactionSession;
use App\Transaction;
use Illuminate\Support\Facades\Http;

class Chargemoney extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://dashboard.charge.money/api/transaction'; // for live
    // const BASE_URL = 'https://dashboard.charge.money/api/test-transaction'; // for live
    const STATUS_API = 'https://dashboard.charge.money/api/get-transaction-details';

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
        if($input['card_type'] == '1') {
            $cardType = 'MASTER';
        } elseif ($input['card_type'] == '2') {
            $cardType = 'VISA';
        } else {
            $cardType = 'VISA';
        }

        $data = [
            'api_key' => $check_assign_mid->api_key,
            'first_name' => $input['first_name'],
            'last_name' => $input['first_name'],
            'email' => $input['email'],
            'address' => $input['address'],
            'customer_order_id' => $input['session_id'],
            'country' => $input['country'],
            'state' => $input['state'],
            'city' => $input['city'],
            'zip' => $input['zip'],
            "ip_address" => $input['request_from_ip'],
            'phone_no' => $input['phone_no'],
            'amount' => $input["converted_amount"],
            'currency' => $input['converted_currency'],
            'card_type' => $cardType,
            'card_no' => $input['card_no'],
            'cvvNumber' => $input['cvvNumber'],
            'ccExpiryYear' => $input['ccExpiryYear'],
            'ccExpiryMonth' => $input['ccExpiryMonth'],
            "response_url" => route("chargemoney.redirect", $input["session_id"]),
            "webhook_url" => route("chargemoney.callback", $input["session_id"]),
        ];

        $request_url = self::BASE_URL;

        $payment_response = $this->curlPostRequest($request_url, $data);
        $response = json_decode($payment_response, true);

        \Log::info([
            'payload' => json_encode($data),
            'chargemoney-response' => $response
        ]);
        
        if ($response) {   
            $input["gateway_id"] = isset($response["data"]["order_id"]) ? $response["data"]["order_id"] : '1';
            $this->updateGatewayResponseData($input, $response);
            
            if ($response['status'] == "fail") {
                return [
                    'status' => '0',
                    'reason' => $response["message"],
                    'order_id' => $input['order_id'],
                ];
            } else if ($response['status'] == "success") {
                return [
                    'status' => '1',
                    'reason' => 'Your transaction has been processed successfully.',
                    'order_id' => $input['order_id'],
                ];
            } else if ($response["status"] == "3d_redirect") {
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                    'redirect_3ds_url' => $response["redirect_3ds_url"]
                ];
            } else {
                return [
                    'status' => '0',
                    'reason' => 'Your transaction could not processed.',
                    'order_id' => $input['order_id']
                ];
            }

            
        } else {
            $input['status'] = '0';
            $input['reason'] = 'Transaction declined.';
        }

        return $input;
    }

    public function redirect(Request $request, $session_id) {
        \Log::info([
            'chargemoney-redirect' => $request->all(),
        ]);

        $request_data = $request->all();
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        $input['gateway_id'] = isset($request_data['order_id']) ? $request_data['order_id'] : "1";
        // $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);
        if (isset($request_data['status']) && $request_data['status'] == 'success') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
        } else if (isset($request_data['status']) && $request_data['status'] == 'declined' || $request_data['status'] == 'fail') {
            $input['status'] = '0';
            $input['reason'] = (isset($request_data['message']) ? $request_data['message'] : 'Your transaction could not processed.');
        } else if (isset($request_data['status']) && $request_data['status'] == 'blocked') {
            $input['status'] = '5';
            $input['reason'] = (isset($request_data['message']) ? $request_data['message'] : 'Your transaction could not processed.');
        } else {
            $input['status'] = '2';
            $input['reason'] = 'Transaction is in pending.';
        }
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function callback(Request $request, $session_id) {
        \Log::info([
            'chargemoney-callback' => $request->all(),
        ]);
        sleep(10);
        $request_data = $request->all();
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        $input['gateway_id'] = isset($request_data['order_id']) ? $request_data['order_id'] : "1";
        $input["descriptor"] = isset($request_data["descriptor"]) ? $request_data["descriptor"] : "";
        if (isset($request_data['transaction_status']) && $request_data['transaction_status'] == 'success') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
            $this->storeTransaction($input);
        } else if (isset($request_data['transaction_status']) && $request_data['transaction_status'] == 'fail') {
            $input['status'] = '0';
            $input['reason'] = (isset($request_data['reason']) ? $request_data['reason'] : 'Your transaction could not processed.');
            $this->storeTransaction($input);
        } else if (isset($request_data['transaction_status']) && $request_data['transaction_status'] == 'pending') {
            $input['status'] = '2';
            $input['reason'] = "Transaction is in pending.";
            $this->storeTransaction($input);
        } else if (isset($request_data['transaction_status']) && $request_data['transaction_status'] == 'blocked') {
            $input['status'] = '5';
            $input['reason'] = (isset($request_data['reason']) ? $request_data['reason'] : 'Your transaction could not processed.');
            $this->storeTransaction($input);
        }
        exit();
    }

    public function statusApi($request_data, $input)
    {

        $mid = checkAssignMID($input["payment_gateway_id"]);
        $payload = [
            "api_key" => $mid->api_key,
            "order_id" => $request_data["customer_order_id"]
        ];
        // $response = Http::post(self::STATUS_API, $payload)->json();
        $response = $this->curlPostRequest(self::STATUS_API, $payload);
        if (!empty($response) && $response != null) {
            return $response;
        } else {
            return null;
        }
    }

    public function curlPostRequest($url, $data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}
