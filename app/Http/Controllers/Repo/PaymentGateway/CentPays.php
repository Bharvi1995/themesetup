<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Jobs\CentPayTransactionRestoreJob;
use App\Traits\StoreTransaction;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CentPays extends Controller
{

    use StoreTransaction;
    protected $transaction;

    const BASE_URL = "https://centpays.com/v2/process_payment";
    const AUTH_URL = "https://centpays.com/v2/ini_payment/";

    const STATUS_URL = "https://centpays.com/v2/get_transaction";

    public function __construct()
    {
        $this->transaction = new Transaction;
    }

    public function checkout($input, $midDetails)
    {
        try {
            $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');

            $payload = [
                "name" => $input['first_name'] . " " . $input["last_name"],
                "email" => $input['email'],
                "phone" => $input["phone_no"],
                "amount" => $input["converted_amount"],
                "currency" => $input["converted_currency"],
                "transaction_id" => $input["session_id"],
                "order_number" => $input["order_id"],
                "requestMode" => "Card",
                "cardNo" => $input["card_no"],
                "cardExpire" => str_replace("20", "", $input["ccExpiryYear"]) . $input["ccExpiryMonth"],
                "cardCVC" => $input['cvvNumber'],
                "back_url" => route('centpays.callback', [$input['order_id']]),
            ];
            Log::info(["centpay-request-payload" => $payload]);
            // dd($midDetails->api_key, $midDetails->secret_key);
            $response = Http::withHeaders([
                'api-key' => $midDetails->api_key,
                'api-secret' => $midDetails->secret_key,
                'content-type' => 'application/json',
                'mid' => $midDetails->id == 10 ? "MID1_Gaming" : "MID2_Forex"

            ])->post(self::BASE_URL, $payload)->json();

            // * Store the request payload in table

            $payload["cardNo"] = cardMasking($payload["cardNo"]);
            $payload["cardCVC"] = "XXX";
            $this->storeMidPayload($input["session_id"], json_encode($payload));

            $input['gateway_id'] = 1;
            $this->updateGatewayResponseData($input, $response);
            if (empty($response)) {
                return [
                    'status' => '0',
                    'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                    'order_id' => $input['order_id'],
                ];
            } else if ($response['code'] == 202) {
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successful, please redirect.',
                    'redirect_3ds_url' => self::AUTH_URL . $response['token']
                ];
            } else if ($response['code'] == 200) {
                return [
                    'status' => '1',
                    'reason' => 'Your transaction has been processed successfully.',
                    'order_id' => $input['order_id']
                ];
            } else {
                return [
                    'status' => '0',
                    'reason' => $response['message'] ?? "We are facing temporary issue from the bank side. Please contact us for more detail.",
                    'order_id' => $input['order_id'],
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => '0',
                'reason' => $e->getMessage(),
                'order_id' => $input['order_id']
            ];
        }
    }

    // * Redirect callback
    public function redirect(Request $request, $orderId)
    {
        $payload = $request->all();

        $transaction_session = DB::table('transaction_session')
            ->where('order_id', $orderId)
            ->first();
        if ($transaction_session == null) {
            return abort(404);
        }

        $input = json_decode($transaction_session->request_data, true);
        $input["gateway_id"] = isset($payload["Transaction_id"]) ? $payload["Transaction_id"] : "1";

        // Hit the status API to check the status
        if ($input["gateway_id"] != "1") {
            $mid = checkAssignMID($transaction_session->payment_gateway_id);
            $response = $this->statusAPI($mid, ["transaction_id" => $input["gateway_id"]]);
            if ($response["status"] != "Success") {
                $payload["message"] = $response["message"];
            }
            $payload["status"] = $response["status"];
        } else {
            $payload["message"] = "User cancelled the transaction process.";
            $payload['status'] = "Failed";
        }

        if (isset($payload["status"]) && $payload["status"] == "Failed") {
            $input['status'] = '0';
            $input['reason'] = $payload["message"] != null && $payload["message"] != "" ? $payload["message"] : "Your transaction got declined";
        } else if (isset($payload["status"]) && $payload["status"] == "Success") {
            $input['status'] = '1';
            $input['reason'] = $payload["message"] != null && $payload["message"] != "" ? $payload["message"] : "Transaction processed successfully.";
        } else if (isset($payload["status"]) && $payload["status"] == "Droped") {
            $input['status'] = '0';
            $input['reason'] = $payload["message"] != null && $payload["message"] != "" ? $payload["message"] : "User cancelled the payment process.";
        } else if (isset($payload["status"]) && $payload["status"] == "In Progress") {
            $input['status'] = '2';
            $input['reason'] = $payload["message"] != null && $payload["message"] != "" ? $payload["message"] : "Your transaction is under process . Please wait for sometime!";
        } else {
            $input['status'] = '0';
            $input['reason'] = $payload["message"] != null && $payload["message"] != "" ? $payload["message"] : "User cancelled the payment process.";
        }

        $this->updateGatewayResponseData($input, $input["gateway_id"] != "1" ? $response : $payload);
        $this->storeTransaction($input);

        // convert response in query string

        $store_transaction_link = $this->getRedirectLink($input);

        return redirect($store_transaction_link);
    }

    // * Redirect callback
    public function webhhok(Request $request)
    {
        $response = $request->all();

        if (empty($response) || !isset($response['transaction_id'])) {
            abort(404);
        }
        $transactionSession = DB::table('transaction_session')->where('gateway_id', $response['transaction_id'])->first();
        if ($transactionSession == null) {
            abort(404);
        }

        // * Store the transaction webhook 
        $this->storeMidWebhook($transactionSession->transaction_id, json_encode($response));

        $input = json_decode($transactionSession->request_data, true);

        if ($response['status'] != null && $response['status'] == 'Success') {
            $input['status'] = '1';
            $input['reason'] = "Your transaction has been processed successfully.";
        } else if ($response['status'] != null && $response['status'] == 'Failed') {
            $input['status'] = '0';
            $input['reason'] = $response["message"] != null && $response["message"] != "" ? $response["message"] : "Your transaction got declined.";
        } else if (isset($response["status"]) && $response["status"] == "Droped") {
            $input['status'] = '0';
            $input['reason'] = $response["message"] != null && $response["message"] != "" ? $response["message"] : "User cancelled the payment process";
        } else {
            $input['status'] = '2';
            $input['reason'] = "Your transaction is under process. please wait for sometime.";
        }

        $input['gateway_id'] = isset($response['transaction_id']) ? $response['transaction_id'] : "1";
        // $this->updateGatewayResponseData($input, $request->all());
        $this->storeTransaction($input);
    }

    // * Status API 
    public function statusAPI($mid, $payload)
    {
        $response = Http::withHeaders([
            'api-key' => $mid->api_key,
            'api-secret' => $mid->secret_key,
            "Content-type" => "application/json"
        ])->post(self::STATUS_URL, $payload)->json();

        if (empty($response)) {
            return ["message" => "", "status" => ""];
        }
        return $response["data"];
    }

    // * Transaction CRON to restore the pending transactions
    public function restoreTransactions(Request $request)
    {
        try {
            if ($request->password != 'fnsdk34naSdkc23VC111sShiu65ZFG') {
                exit();
            }
            // $midId = "7"; // test MID
            $midId = "10"; // live MID

            $check_assign_mid = checkAssignMid($midId);
            CentPayTransactionRestoreJob::dispatch($check_assign_mid, self::STATUS_URL);
            return response()->json(["status" => 200, "message" => "Retore transactions job added successfully!"]);
        } catch (\Exception $err) {
            return response()->json(["status" => 200, "message" => $err->getMessage()]);
        }
    }



}