<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Jobs\KryptovaPendingTransactionJob;
use App\Traits\StoreTransaction;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\TransactionSession;
use Illuminate\Support\Facades\Http;

class Kryptova extends Controller
{

    use StoreTransaction;

    protected $transaction;

    // const BASE_URL = "https://hello.kryptova.biz/api/test-transaction";
    const BASE_URL = "https://hello.kryptova.biz/api/transaction";

    const STATUS_URL = "https://hello.kryptova.biz/api/get-transaction-details";

    public function __construct()
    {
        $this->transaction = new Transaction;
    }

    public function checkout($input, $check_assign_mid)
    {
        $payload = [
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
            "customer_order_id" => $input["order_id"],
            "response_url" => route("kryptova.response", $input["session_id"]),
            "webhook_url" => route("kryptova.webhook", $input["session_id"]),
        ];

        // * Hit the API 
        $response = Http::post(self::BASE_URL, $payload)->json();
        $input["gateway_id"] = "1";
        $this->updateGatewayResponseData($input, $response);
        // Log::info(["Kryptova-response" => $response]);
        if (empty($response)) {
            return [
                'status' => '0',
                'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                'order_id' => $input['order_id'],
            ];
        } else if ($response['status'] == "fail") {
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
        $input['gateway_id'] = isset($request_data['order_id']) ? $request_data['order_id'] : "1";
        // $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);
        if (isset($request_data['status']) && $request_data['status'] == 'success') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
        } else if (isset($request_data['status']) && $request_data['status'] == 'fail') {
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

    public function webhook(Request $request, $session_id)
    {
        $request_data = $request->all();
        // Log::info([
        //     'kryptova_webhook_data' => $request_data,
        //     'id' => $session_id
        // ]);
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        $input['gateway_id'] = isset($request_data['order_id']) ? $request_data['order_id'] : "1";
        if (isset($request_data['transaction_status']) && $request_data['transaction_status'] == 'success') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
            $this->storeTransaction($input);
        } else if (isset($request_data['transaction_status']) && $request_data['transaction_status'] == 'fail') {
            $input['status'] = '0';
            $input['reason'] = (isset($request_data['reason']) ? $request_data['reason'] : 'Your transaction could not processed.');
            $this->storeTransaction($input);
        } else if (isset($request_data['transaction_status']) && $request_data['transaction_status'] == 'blocked') {
            $input['status'] = '5';
            $input['reason'] = (isset($request_data['reason']) ? $request_data['reason'] : 'Your transaction could not processed.');
            $this->storeTransaction($input);
        }
        exit();
    }

    // * Update pending transactions 
    public function updatePendingTx(Request $request)
    {
        try {
            if ($request->get("password") != "f8d3h5883e7e4318608a0184f3445545e3e56489") {
                abort(404);
            }
            $mid = checkAssignMID("9");
            KryptovaPendingTransactionJob::dispatch($mid, self::STATUS_URL);
            return response()->json(["status" => 200, "message" => "job added successfully!"]);
        } catch (\Exception $err) {
            return response()->json(["status" => 500, "message" => $err->getMessage()]);

        }
    }
}