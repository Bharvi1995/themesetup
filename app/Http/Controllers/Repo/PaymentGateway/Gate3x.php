<?php
namespace App\Http\Controllers\Repo\PaymentGateway;


use DB;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Http;

class Gate3x extends Controller
{

    use StoreTransaction;

    const BASE_URL = "https://api.exxogate.com/payments/link";
    const STATUS_API = "https://api.exxogate.com/payments/status";

    public function checkout($input, $midDetails)
    {
        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');

        $payload = [
            "amount" => $input["converted_amount"],
            "currency" => $input["converted_currency"],
            "description" => "testpay transaction for " . $input["order_id"],
            "invoiceId" => $input["session_id"],
            "accountId" => $input["email"],
            "ip" => $input["ip_address"],
            "locale" => "en_US",
            "data" => [
                "personId" => $input["upi"],
                "phone" => $input["phone_no"],
                "address" => $input["address"],
                "city" => $input["city"],
                "firstName" => $input["first_name"],
                "lastName" => $input["last_name"],
                "countryCode" => $input["country"],
                "paymentTypeId" => "1"
            ],
            "successUrl" => route('gate3x.success', [$input["session_id"]]),
            "failureUrl" => route('gate3x.fail', [$input["session_id"]]),
            "pendingUrl" => route('gate3x.pending', [$input["session_id"]]),
            "cancelUrl" => route('gate3x.cancel', [$input["session_id"]]),
        ];

        $response = Http::withBasicAuth($midDetails->public_key, $midDetails->secret_key)->withHeaders(["Content-type" => "application/json"])->post(self::BASE_URL, $payload)->json();

        // * Store mid payload
        $this->storeMidPayload($input["session_id"], json_encode($payload));

        Log::info(["payload" => $payload, "gate3x-res" => $response]);
        if (empty($response) || $response == null) {
            return [
                'status' => '0',
                'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                'order_id' => $input['order_id'],
            ];
        }

        // * Get and update the gateway id and response
        $input["gateway_id"] = isset($response["model"]["transactionId"]) ? $response["model"]["transactionId"] : "1";
        $this->updateGatewayResponseData($input, $response);


        if (isset($response["success"]) && $response["success"] == true && isset($response["link"]["action"])) {
            return [
                'status' => '7',
                'reason' => '3DS link generated successful, please redirect.',
                'redirect_3ds_url' => $response["link"]["action"]
            ];
        } else if (isset($response["model"]["statusCode"]) && $response["model"]["statusCode"] == 4) {
            return [
                "status" => "1",
                "reason" => "Transaction processed successfully!"
            ];
        } else if (isset($response["model"]["statusCode"]) && $response["model"]["statusCode"] == 99) {
            return [
                "status" => "0",
                "reason" => isset($response["model"]["reason"]) ? $response["model"]["reason"] : "Transaction could not processed."
            ];
        } else {
            return [
                "status" => "0",
                "reason" => isset($response["model"]["reason"]) ? $response["model"]["reason"] : "Transaction could not processed."
            ];
        }
    }

    // * for success transaction 
    public function success(Request $request, $id)
    {
        return $this->commonConditions($id);
    }

    // * for pending transaction 
    public function pending(Request $request, $id)
    {
        return $this->commonConditions($id);
    }

    // * for fail transaction 
    public function fail(Request $request, $id)
    {
        return $this->commonConditions($id);

    }

    // * for cancel transaction 
    public function cancel(Request $request, $id)
    {
        return $this->commonConditions($id);

    }


    // * Status API
    public function statusApi($mid, $id)
    {
        $response = Http::withBasicAuth($mid->public_key, $mid->secret_key)->withHeaders(["Content-type" => "application/json"])->post(self::STATUS_API, ["transactionId" => $id])->json();

        return $response;
    }

    // * Common conditions checks
    public function commonConditions($id)
    {
        $transaction = DB::table("transaction_session")->select("id", "request_data", "payment_gateway_id", "gateway_id")->where("transaction_id", $id)->first();

        if ($transaction == null) {
            abort(404);
        }

        $input = json_decode($transaction->request_data, true);
        // * hit the status API
        $mid = checkAssignMID($transaction->payment_gateway_id);
        $response = $this->statusApi($mid, $transaction->gateway_id);

        if (isset($response["model"]["statusCode"]) && $response["model"]["statusCode"] == 99) {
            $input["status"] = "0";
            $input["reason"] = isset($response["model"]["reason"]) ? $response["model"]["reason"] : "Transaction could not processed.";
        } else if (isset($response["model"]["statusCode"]) && $response["model"]["statusCode"] == 4) {
            $input["status"] = "1";
            $input["reason"] = "Transaction processed successfully!";
        } else if (isset($response["model"]["statusCode"]) && $response["model"]["statusCode"] == 10) {
            $input["status"] = "0";
            $input["reason"] = "User not completed the transaction process.";
        } else if (isset($response["model"]["statusCode"]) && $response["model"]["statusCode"] == 1) {
            $input["status"] = "2";
            $input["reason"] = "Waiting for 3DS authentication.";
        } else {
            $input["status"] = "0";
            $input["reason"] = isset($response["model"]["reason"]) ? $response["model"]["reason"] : "Transaction could not processed.";
        }

        $this->updateGatewayResponseData($input, $response);
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);

    }
}