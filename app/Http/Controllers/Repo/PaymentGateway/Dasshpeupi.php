<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use Http;
use RuntimeException;
use App\Transaction;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Dasshpeupi extends Controller
{
    use StoreTransaction;

    // const BASE_URL = 'https://uat.dasshpe.com/crm/jsp/merchantpay'; // it's test url
    const BASE_URL = 'https://secure.dasshpe.com/crm/jsp/merchantpay'; // it's live url

    public function checkout($input, $check_assign_mid)
    {
        return [
            'status' => '7',
            'reason' => '3DS link generated successful, please redirect.',
            'redirect_3ds_url' => route('dasshpeupi.form', $input["session_id"])
        ];
    }

    // * Auth form show
    public function form(Request $request, $session_id)
    {
        $transaction_session = DB::table('transaction_session')
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->where('transaction_id', $session_id)
            ->first();
        if (empty($transaction_session)) {
            abort(404);
        }

        $input = json_decode($transaction_session->request_data, 1);
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');

        $payload = [
            'AMOUNT' => $input['converted_amount'] * 100,
            'CURRENCY_CODE' => 356,
            'CUST_EMAIL' => $input['email'],
            'CUST_NAME' => $input['first_name'] . ' ' . $input['last_name'],
            'CUST_PHONE' => $input['phone_no'],
            'HASH' => $this->generateHash($input, $check_assign_mid),
            'MOP_TYPE' => 'UP',
            'ORDER_ID' => $input['session_id'],
            'PAYMENT_TYPE' => 'UP',
            'PAY_ID' => $check_assign_mid->pay_id,
            'PRODUCT_DESC' => 'testpay sale ' . $input['first_name'] . ' ' . $input['last_name'],
            'RETURN_URL' => route('dasshpeupi.return', $input['session_id']),
            'TXNTYPE' => 'SALE',
            'UPI' => $input['upi'],
        ];
        $this->storeMidPayload($input["session_id"], json_encode($payload));
        $url = self::BASE_URL;
        return view('gateway.dasshpeupi.form', compact('payload', 'url'));

    }

    // * Return callback
    public function return (Request $request, $id)
    {
        $response = $request->all();
        $transaction = DB::table("transaction_session")->select("id", "request_data")->where("transaction_id", $id)->first();

        if ($transaction == null) {
            abort(404);
        }

        $input = json_decode($transaction->request_data, true);

        if (isset($response["RESPONSE_CODE"]) && $response["RESPONSE_CODE"] == "000") {
            $input["status"] = "1";
            $input["reason"] = "Transaction processed successfully!";
        } else if (isset($response["RESPONSE_CODE"]) && $response["RESPONSE_CODE"] == "002" || $response["RESPONSE_CODE"] == "004") {
            $input["status"] = "0";
            $input["reason"] = isset($response["RESPONSE_MESSAGE"]) ? $response["RESPONSE_MESSAGE"] : "Transaction could not processed.";
        } else if (isset($response["RESPONSE_CODE"]) && $response["RESPONSE_CODE"] == "014") {
            $input["status"] = "2";
            $input["reason"] = "Transaction is under process. Please wait for sometime.";
        } else {
            $input["status"] = "0";
            $input["reason"] = isset($response["RESPONSE_MESSAGE"]) ? $response["RESPONSE_MESSAGE"] : "Transaction could not processed.";
        }

        $input["gateway_id"] = $response['TXN_ID'] ?? "1";
        $this->updateGatewayResponseData($input, $response);
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    // * Generate the hash 
    public function generateHash($input, $check_assign_mid)
    {
        // :Todo Have to add MOP Type
        $hashStr = "AMOUNT=" . $input['converted_amount'] * 100 . "~CURRENCY_CODE=356~CUST_EMAIL=" . $input["email"] . "~CUST_NAME=" . $input["first_name"] . " " . $input["last_name"] . "~CUST_PHONE=" . $input["phone_no"] . "~MOP_TYPE=UP~ORDER_ID=" . $input["session_id"] . "~PAYMENT_TYPE=UP~PAY_ID=" . $check_assign_mid->pay_id . "~PRODUCT_DESC=" . "testpay sale " . $input["first_name"] . " " . $input["last_name"] . "~RETURN_URL=" . route("dasshpeupi.return", $input["session_id"]) . "~TXNTYPE=SALE~UPI=" . $input['upi'];

        $addedSecretKey = $hashStr . $check_assign_mid->secret_key;

        $shaStr = hash("sha256", $addedSecretKey);
        $hash = strtoupper($shaStr);

        return $hash;
    }
}