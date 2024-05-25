<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use App\Transaction;
use Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class Dasshpe extends Controller
{

    use StoreTransaction;
    const BASE_URL = "https://secure.dasshpe.com/crm/jsp/merchantpay"; // IT'S Live URL

    public function checkout($input, $midDetails)
    {
        $mop = "VI";
        if ($input["card_type"] == "2") {
            $mop = "VI";
        } else if ($input["card_type"] == "3") {
            $mop = "MC";
        } else if ($input["card_type"] == "6") {
            $mop = "MS";
        }
        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');

        $payload = [
            "AMOUNT" => $input["converted_amount"] * 100,
            "CURRENCY_CODE" => 356,
            "CUST_EMAIL" => $input["email"],
            "CUST_NAME" => $input["first_name"] . " " . $input["last_name"],
            "CUST_PHONE" => $input["phone_no"],
            "HASH" => $this->generateHash($input, $midDetails, $input["converted_amount"] * 100, $mop),
            "MOP_TYPE" => $mop,
            "ORDER_ID" => $input["session_id"],
            "PAYMENT_TYPE" => "DC",
            "PAY_ID" => $midDetails->pay_id,
            "PRODUCT_DESC" => "testpay sale " . $input["first_name"] . " " . $input["last_name"],
            "RETURN_URL" => route("dasshpe.return", [$input["session_id"]]),
            "TXNTYPE" => "SALE",
            "CARD_NUMBER" => $input["card_no"],
            "CARD_EXP_DT" => $input["ccExpiryMonth"] . $input["ccExpiryYear"],
            "CVV" => $input["cvvNumber"],
        ];

        $response = Http::asForm()->post(self::BASE_URL, $payload)->body();

        // * Add mid payload
        $payload["CARD_NUMBER"] = cardMasking($payload["CARD_NUMBER"]);
        $payload["CVV"] = "XXX";
        $this->storeMidPayload($input["session_id"], json_encode($payload));

        if (!empty($response)) {
            $input["gateway_id"] = "1";
            $this->updateGatewayResponseData($input, $response);
            return [
                'status' => '7',
                'reason' => '3DS link generated successful, please redirect.',
                'redirect_3ds_url' => route('dasshpe.auth.form', [$input["session_id"]])
            ];
        }
        return [
            'status' => '0',
            'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
            'order_id' => $input['order_id'],
        ];

    }

    // * Auth form show
    public function authForm(Request $request, $id)
    {
        $transaction = DB::table("transaction_session")->select("id", "request_data", "response_data")->where("transaction_id", $id)->first();
        $form = json_decode($transaction->response_data, true);
        return view('gateway.dasshpe.authForm', compact('form'));

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

        $input["gateway_id"] = "1";
        $this->updateGatewayResponseData($input, $response);
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    // * Generate the hash 
    public function generateHash($input, $mid, $amount, $mop)
    {
        // :Todo Have to add MOP Type
        $hashStr = "AMOUNT=" . $amount . "~CARD_EXP_DT=" . $input["ccExpiryMonth"] . $input["ccExpiryYear"] . "~CARD_NUMBER=" . $input["card_no"] . "~CURRENCY_CODE=356~CUST_EMAIL=" . $input["email"] . "~CUST_NAME=" . $input["first_name"] . " " . $input["last_name"] . "~CUST_PHONE=" . $input["phone_no"] . "~CVV=" . $input["cvvNumber"] . "~MOP_TYPE=" . $mop . "~ORDER_ID=" . $input["session_id"] . "~PAYMENT_TYPE=DC" . "~PAY_ID=" . $mid->pay_id . "~PRODUCT_DESC=" . "testpay sale " . $input["first_name"] . " " . $input["last_name"] . "~RETURN_URL=" . route("dasshpe.return", [$input["session_id"]]) . "~TXNTYPE=SALE";

        $addedSecretKey = $hashStr . $mid->secret_key;

        $shaStr = hash("sha256", $addedSecretKey);
        $hash = strtoupper($shaStr);

        Log::info(["the-hash-string" => $addedSecretKey, "Hash" => $hash]);
        return $hash;
    }
}