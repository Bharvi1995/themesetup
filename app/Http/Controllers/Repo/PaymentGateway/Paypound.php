<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Session;
use App\Transaction;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;

class Paypound extends Controller
{
    use StoreTransaction;

    protected $transaction;

    //const BASE_URL = 'https://portal.paypound.ltd/api/test-transaction';
    const BASE_URL = 'https://portal.paypound.ltd/api/transaction';

    public function __construct()
    {
        $this->transaction = new Transaction;
    }

    public function checkout($input, $check_assign_mid)
    {
        $curl = curl_init();
        $data = [
            "api_key" => $check_assign_mid->api_key,
            "last_name" => $input["last_name"],
            "first_name" => $input["first_name"],
            "address" => $input["address"],
            "city" => $input["city"],
            "state" => $input["state"],
            "country" => $input["country"],
            "zip" => $input["zip"],
            "ip_address" => "18.134.91.249",
            'email' => $input['email'],
            "phone_no" => $input["phone_no"],
            "amount" => $input['converted_amount'],
            "currency" => $input["converted_currency"],
            "card_no" => $input["card_no"],
            "ccExpiryMonth" => $input["ccExpiryMonth"],
            "ccExpiryYear" => $input["ccExpiryYear"],
            "cvvNumber" => $input["cvvNumber"],
            "customer_order_id" => $input["session_id"],
            "response_url" => route("paypound.response", $input["session_id"]),
            "webhook_url" => route("paypound.webhook", $input["session_id"]),
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
                'reason' => 'Your transaction has been processed successfully.',
                'order_id' => $input['order_id'],
            ];
        } elseif (isset($response_data->status) && $response_data->status == "fail") {
            return [
                'status' => '0',
                'reason' => (isset($response_data->message) ? $response_data->message : 'Your transaction could not processed.'),
                'order_id' => $input['order_id']
            ];
        } elseif (isset($response_data->status) && $response_data->status == "3d_redirect") {
            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                'redirect_3ds_url' => $response_data->redirect_3ds_url
            ];
        } elseif (isset($response_data->status) && $response_data->status == "blocked") {
            return [
                'status' => '5',
                'reason' => (isset($response_data->message) ? $response_data->message : 'Your transaction could not processed.'),
                'order_id' => $input['order_id']
            ];
        } elseif (isset($response_data->status) && $response_data->status == "pending") {
            return [
                'status' => '2',
                'reason' => 'Transaction is pending in acquirer system, please check after few minutes.',
                'order_id' => $input['order_id']
            ];
        } else {
            return [
                'status' => '0',
                'reason' => 'Your transaction could not processed.',
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

        $get_status = $this->getStatus($input);
        if (isset($get_status['status']) && $get_status['status'] == 'success') {
            $input['gateway_id'] = $get_status['transaction']['order_id'] ?? null;
            if (isset($get_status['transaction']['transaction_status']) && $get_status['transaction']['transaction_status'] == 'success') {
                $input['status'] = '1';
                $input['reason'] = 'Transaction processed successfully.';
            } else if (isset($get_status['transaction']['transaction_status']) && $get_status['transaction']['transaction_status'] == 'declined') {
                $input['status'] = '0';
                $input['reason'] = $get_status['transaction']['reason'] ?? 'Transaction not processed.';
            } else if (isset($get_status['transaction']['transaction_status']) && $get_status['transaction']['transaction_status'] == 'blocked') {
                $input['status'] = '5';
                $input['reason'] = $get_status['transaction']['reason'] ?? 'Transaction was blocked.';
            } else if (isset($get_status['transaction']['transaction_status']) && $get_status['transaction']['transaction_status'] == 'pending') {
                $input['status'] = '2';
                $input['reason'] = $get_status['transaction']['reason'] ?? 'Transaction is pending.';
            } else {
                \Log::info(['paypound_status_else' => $get_status]);
                $input['status'] = '0';
                $input['reason'] = 'Transaction not processed.';
            }
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        } else {
            \Log::info(['paypound_redirect_else' => $request_data]);
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
        $get_status = $this->getStatus($input);
        if (isset($get_status['status']) && $get_status['status'] == 'success') {
            $input['gateway_id'] = $get_status['transaction']['order_id'] ?? null;
            if (isset($get_status['transaction']['transaction_status']) && $get_status['transaction']['transaction_status'] == 'success') {
                $input['status'] = '1';
                $input['reason'] = 'Transaction processed successfully.';
            } else if (isset($get_status['transaction']['transaction_status']) && $get_status['transaction']['transaction_status'] == 'declined') {
                $input['status'] = '0';
                $input['reason'] = $get_status['transaction']['reason'] ?? 'Transaction not processed.';
            } else if (isset($get_status['transaction']['transaction_status']) && $get_status['transaction']['transaction_status'] == 'blocked') {
                $input['status'] = '5';
                $input['reason'] = $get_status['transaction']['reason'] ?? 'Transaction was blocked.';
            } else if (isset($get_status['transaction']['transaction_status']) && $get_status['transaction']['transaction_status'] == 'pending') {
                $input['status'] = '2';
                $input['reason'] = $get_status['transaction']['reason'] ?? 'Transaction is pending.';
            } else {
                \Log::info(['paypound_status_else' => $get_status]);
                exit();
            }
            $transaction_response = $this->storeTransaction($input);
            exit();
        } else {
            \Log::info(['paypound_webhook_else' => $request_data]);
        }
        exit();
    }

    public function getStatus($input)
    {
        $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);

        $status_url = 'https://portal.paypound.ltd/api/get-transaction-details';

        $status_data = [
            'api_key' => $check_assign_mid->api_key,
            'customer_order_id' => $input['session_id'],
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
