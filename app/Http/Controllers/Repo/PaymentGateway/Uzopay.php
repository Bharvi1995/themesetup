<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Session;
use App\Transaction;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;

class Uzopay extends Controller
{
    use StoreTransaction;

    protected $transaction;

    // const BASE_URL = 'https://portal.uzopay.com/api/test-transaction';
    const BASE_URL = 'https://portal.uzopay.com/api/transaction';

    public function __construct()
    {
        $this->transaction = new Transaction;
    }

    public function checkout($input, $check_assign_mid)
    {
        $curl = curl_init();
        $data = [
            "api_key" => $check_assign_mid->api_key,
            "last_name" => $input["user_last_name"],
            "first_name" => $input["user_first_name"],
            "address" => $input["user_address"],
            "city" => $input["user_city"],
            "state" => $input["user_state"],
            "country" => $input["user_country"],
            "zip" => $input["user_zip"],
            "ip_address" => $input["request_from_ip"],
            'email' => $input['user_email'],
            "phone_no" => $input["user_phone_no"],
            "amount" => $input['converted_amount'],
            "currency" => $input["converted_currency"],
            "card_no" => $input["user_card_no"],
            "ccExpiryMonth" => $input["user_ccexpiry_month"],
            "ccExpiryYear" => $input["user_ccexpiry_year"],
            "cvvNumber" => $input["user_cvv_number"],
            "customer_order_id" => $input["session_id"],
            "response_url" => route("uzopay.response", $input["session_id"]),
            // "response_url" => "https://webhook.site/60aafdff-eeeb-46e8-ad83-f989ca7de764",
            // "webhook_url" => "https://webhook.site/60aafdff-eeeb-46e8-ad83-f989ca7de764",
            "webhook_url" => route("uzopay.webhook", $input["session_id"]),
        ];
        curl_setopt_array($curl, array(
            CURLOPT_URL => self::BASE_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response_data = json_decode($response);
        if (isset($response_data)) {
            if (isset($response_data->data->order_id) && !empty($response_data->data->order_id)) {
                $input['gateway_id'] = $response_data->data->order_id;
            }
            $this->updateGatewayResponseData($input, $response_data);
        }
        if (isset($response_data->status) && $response_data->status == "success") {
            return [
                'status' => '1',
                'reason' => 'Your payment has been successfully completed.',
                'order_id' => $input['order_id'],
            ];
        } elseif (isset($response_data->status) && $response_data->status == "fail") {
            return [
                'status' => '0',
                'reason' => (isset($response_data->message) ? $response_data->message : 'The transaction was unsuccessful.'),
                'order_id' => $input['order_id']
            ];
        } elseif (isset($response_data->status) && $response_data->status == "3d_redirect") {
            return [
                'status' => '7',
                'reason' => "Please redirect to the specified 'payment_link' to complete the transaction processing.",
                'payment_link' => $response_data->redirect_3ds_url
            ];
        } elseif (isset($response_data->status) && $response_data->status == "blocked") {
            return [
                'status' => '5',
                'reason' => (isset($response_data->message) ? $response_data->message : 'The transaction was unsuccessful.'),
                'order_id' => $input['order_id']
            ];
        } elseif (isset($response_data->status) && $response_data->status == "pending") {
            return [
                'status' => '2',
                'reason' => 'Your transaction is under process . Please wait for sometime!',
                'order_id' => $input['order_id']
            ];
        } else {
            return [
                'status' => '0',
                'reason' => 'The transaction was unsuccessful.',
                'order_id' => $input['order_id']
            ];
        }
    }

    public function redirect(Request $request, $session_id)
    {
        $request_data = $request->all();

        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        $status_code = $this->getStatus($input);
        if (isset($status_code['status']) && $status_code['status'] == 'success') {
            $input['gateway_id'] = $status_code['transaction']['order_id'] ?? null;
            if (isset($status_code['transaction']['transaction_status']) && $status_code['transaction']['transaction_status'] == 'success') {
                $input["gateway_id"] = $status_code['transaction']["order_id"];
                $input['status'] = '1';
                $input['reason'] = 'Your payment has been successfully completed.';
            } else if (isset($status_code['transaction']['transaction_status']) && $status_code['transaction']['transaction_status'] == 'declined') {
                $input['status'] = '0';
                $input['reason'] = $status_code['transaction']['reason'] ?? 'The transaction was unsuccessful.';
            } else if (isset($status_code['transaction']['transaction_status']) && $status_code['transaction']['transaction_status'] == 'blocked') {
                $input['status'] = '5';
                $input['reason'] = $status_code['transaction']['reason'] ?? 'Transaction was blocked.';
            } else if (isset($status_code['transaction']['transaction_status']) && $status_code['transaction']['transaction_status'] == 'pending') {
                $input['status'] = '2';
                $input['reason'] = $status_code['transaction']['reason'] ?? 'Transaction is pending.';
            } else {
                $input['status'] = '0';
                $input['reason'] = 'The transaction was unsuccessful.';
            }
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        }
    }

    public function webhook(Request $request, $session_id)
    {
        $request_data = $request->all();
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        if (isset($request_data['status']) && $request_data['status'] == 'success') {
            $input['gateway_id'] = $request_data['transaction']['order_id'] ?? null;
            if (isset($request_data['transaction']['transaction_status']) && $request_data['transaction']['transaction_status'] == 'success') {
                $input['status'] = '1';
                $input['reason'] = 'Your payment has been successfully completed.';
            } else if (isset($request_data['transaction']['transaction_status']) && $request_data['transaction']['transaction_status'] == 'declined') {
                $input['status'] = '0';
                $input['reason'] = $request_data['transaction']['reason'] ?? 'The transaction was unsuccessful.';
            } else if (isset($request_data['transaction']['transaction_status']) && $request_data['transaction']['transaction_status'] == 'blocked') {
                $input['status'] = '5';
                $input['reason'] = $request_data['transaction']['reason'] ?? 'Transaction was blocked.';
            } else if (isset($request_data['transaction']['transaction_status']) && $request_data['transaction']['transaction_status'] == 'pending') {
                $input['status'] = '2';
                $input['reason'] = $request_data['transaction']['reason'] ?? 'Transaction is pending.';
            }
            $transaction_response = $this->storeTransaction($input);
        } 
        exit();
    }

    public function getStatus($input)
    {
        $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);

        $status_url = 'https://portal.uzopay.com/api/get-transaction-details';

        $status_data = [
            'api_key' => $check_assign_mid->api_key,
            'order_id' => $input['session_id'],
        ];

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $status_url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($status_data));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);

        if (isset($error_msg) && !empty($error_msg)) {
            return false;
            \Log::info(['paypound_get_status' => $error_msg]);
        } else {
            return json_decode($response, true);
        }
    }
}
