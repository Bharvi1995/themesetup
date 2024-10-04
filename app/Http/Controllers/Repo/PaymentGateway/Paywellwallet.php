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

class Paywellwallet extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://console.paywellwallet.com/api/transaction'; // for live
    // const BASE_URL = 'https://console.paywellwallet.com/api/test/transaction'; // for live
    const STATUS_API = 'https://console.paywellwallet.com/api/get-transaction-details';

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
            'first_name' => $input['user_first_name'],
            'last_name' => $input['user_last_name'],
            'email' => $input['user_email'],
            'address' => $input['user_address'],
            'customer_order_id' => $input['session_id'],
            'country' => $input['user_country'],
            'state' => $input['user_state'],
            'city' => $input['user_city'],
            'zip' => $input['user_zip'],
            "ip_address" => $input['request_from_ip'],
            'phone_no' => $input['user_phone_no'],
            'amount' => $input["converted_amount"],
            'currency' => $input['converted_currency'],
            'card_no' => $input['user_card_no'],
            'cvvNumber' => $input['user_cvv_number'],
            'ccExpiryYear' => $input['user_ccexpiry_year'],
            'ccExpiryMonth' => $input['user_ccexpiry_month'],
            "response_url" => route("paywellwallet.redirect", $input["session_id"]),
            "webhook_url" => route("paywellwallet.callback", $input["session_id"]),
            // "webhook_url" => "https://webhook.site/772fe439-2c82-4d9b-a425-f4eacc61fe1a",
        ];

        $request_url = self::BASE_URL;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $request_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer '.$check_assign_mid->api_key
            ),
        ));

        $payment_response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($payment_response, true);
        \Log::info([
            'payload' => json_encode($data),
            'paywellwallet-response' => $response
        ]);
        
        if ($response) {   
            $input["gateway_id"] = isset($response["data"]["order_id"]) ? $response["data"]["order_id"] : '1';
            $this->updateGatewayResponseData($input, $response);
            
            if ($response['responseCode'] == "0") {
                return [
                    'status' => '0',
                    'reason' => $response["message"],
                    'order_id' => $input['order_id'],
                ];
            } else if ($response['responseCode'] == "1") {
                return [
                    'status' => '1',
                    'reason' => 'Your transaction has been processed successfully.',
                    'order_id' => $input['order_id'],
                ];
            } else if ($response["responseCode"] == "7") {
                return [
                    'status' => '7',
                    'reason' => "Please redirect to the specified 'payment_link' to complete the transaction processing.",
                    'payment_link' => $response["3dsUrl"]
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
            'paywellwallet-redirect' => $request->all(),
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
        if (isset($request_data['responseCode']) && $request_data['responseCode'] == '1') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
        } else if (isset($request_data['responseCode']) && $request_data['responseCode'] == '0' || $request_data['responseCode'] == '3') {
            $input['status'] = '0';
            $input['reason'] = (isset($request_data['message']) ? $request_data['message'] : 'Your transaction could not processed.');
        } else if (isset($request_data['responseCode']) && $request_data['responseCode'] == '5') {
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
            'paywellwallet-callback' => $request->all(),
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
        if (isset($request_data['responseCode']) && $request_data['responseCode'] == '1') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
            $this->storeTransaction($input);
        } else if (isset($request_data['responseCode']) && $request_data['responseCode'] == '0') {
            $input['status'] = '0';
            $input['reason'] = (isset($request_data['reason']) ? $request_data['reason'] : 'Your transaction could not processed.');
            $this->storeTransaction($input);
        } else if (isset($request_data['responseCode']) && $request_data['responseCode'] == '2') {
            $input['status'] = '2';
            $input['reason'] = "Transaction is in pending.";
            $this->storeTransaction($input);
        } else if (isset($request_data['responseCode']) && $request_data['responseCode'] == '5') {
            $input['status'] = '5';
            $input['reason'] = (isset($request_data['reason']) ? $request_data['reason'] : 'Your transaction could not processed.');
            $this->storeTransaction($input);
        }
        exit();
    }
}
