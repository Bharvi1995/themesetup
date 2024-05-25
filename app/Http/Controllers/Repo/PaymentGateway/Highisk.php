<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Jobs\HighiskPendingTxnJob;
use DB;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Http;
use Illuminate\Http\Response;

class Highisk extends Controller
{
    use StoreTransaction;

    const BASE_URL = "https://process.highisk.com/member/remote_charge.asp";

    const STATUS_URL = "https://process.highisk.com/member/getStatus.asp";

    public function checkout($input, $midDetails)
    {
        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');
        $TransType = "0";
        $TypeCredit = "1";
        $currency = "2"; // EUR =2 & 1 = USD

        $concatStr = $midDetails->merchant_no . $TransType . $TypeCredit . $input["converted_amount"] . $currency . $input["card_no"] . $midDetails->hash;
        $shaStr = hash("sha256", $concatStr);
        $signature = base64_encode($shaStr);

        $payload = [
            "CompanyNum" => $midDetails->merchant_no,
            "TransType" => $TransType,
            "CardNum" => $input["card_no"],
            "ExpMonth" => $input["ccExpiryMonth"],
            "ExpYear" => $input["ccExpiryYear"],
            "Member" => $input["first_name"] . " " . $input["last_name"],
            "TypeCredit" => $TypeCredit,
            "Amount" => $input["converted_amount"],
            "Currency" => $currency,
            "CVV2" => $input["cvvNumber"],
            "Email" => $input["email"],
            "Signature" => $signature,
            "PhoneNumber" => $input["phone_no"],
            "BillingAddress1" => $input["address"],
            "BillingCity" => $input["city"],
            "BillingZipCode" => $input["zip"],
            "BillingCountry" => $input["country"],
            "Order" => $input["session_id"],
            "ClientIP" => $input["ip_address"],
            "DateOfBirth" => $this->getDob(),
            "RetURL" => route('highisk.return', [$input["session_id"]]),
            "notification_url" => route('highisk.webhook', [$input["session_id"]])
        ];

        $httpReq = Http::asForm()->post(self::BASE_URL, $payload)->body();
        $response = $this->responseBodyToJson($httpReq)->getData();
        // Log::info(["highisk-response" => $response]);

        // * Store the payload
        $payload["CardNum"] = cardMasking($payload["CardNum"]);
        $payload["CVV2"] = "XXX";
        $this->storeMidPayload($input["session_id"], json_encode($payload));
        $input["gateway_id"] = isset($response->TransID) ? $response->TransID : "1";
        $input["descriptor"] = isset($response->Descriptor) ? $response->Descriptor : null;
        $this->updateGatewayResponseData($input, $response);

        if (empty($response)) {
            return [
                'status' => '0',
                'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                'order_id' => $input['order_id'],
            ];
        } else if (isset($response->Reply) && $response->Reply == "000") {
            return [
                'status' => '1',
                'reason' => $response->ReplyDesc ?? 'Your transaction has been processed successfully.',
                'order_id' => $input['order_id']
            ];
        } else if (isset($response->Reply) && $response->Reply == "553") {
            return [
                'status' => '7',
                'reason' => '3DS link generated successful, please redirect.',
                'redirect_3ds_url' => $response->D3Redirect
            ];
        } else {
            return [
                'status' => '0',
                'reason' => $response->ReplyDesc ?? "We are facing temporary issue from the bank side. Please contact us for more detail.",
                'order_id' => $input['order_id'],
            ];
        }


    }

    // * Return URL
    public function return (Request $request, $id)
    {
        $response = $request->all();
        // Log::info(["return-response" => $response]);
        $transaction = DB::table('transaction_session')->select("payment_gateway_id", "request_data")->where("transaction_id", $id)->first();

        if ($transaction == null) {
            exit();
        }

        $input = json_decode($transaction->request_data, true);
        $mid = checkAssignMID($transaction->payment_gateway_id);
        $statusRes = $this->getStatus($mid, $id);
        // Log::info(["status-api-res" => $statusRes]);
        if ($statusRes != null && count($statusRes) > 0) {
            $response["Reply"] = $statusRes["replyCode"];
            $input["gateway_id"] = $statusRes["trans_id"];
        }

        if (isset($response["Reply"]) && $response["Reply"] == "000") {
            $input["status"] = "1";
            $input["reason"] = "Your transaction has been processed successfully!";
        } else if (isset($response["Reply"]) && $response["Reply"] == "553") {
            $input["status"] = "2";
            $input["reason"] = isset($response["ReplyDesc"]) ? $response["ReplyDesc"] : "Your transaction is under process . Please wait for sometime!";
        } else {
            $input["status"] = "0";
            $input["reason"] = isset($response["ReplyDesc"]) ? $response["ReplyDesc"] : "Your transaction could not processed!";
        }

        $this->updateGatewayResponseData($input, $response);
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    // * Webhook URL
    public function webhook(Request $request, $id)
    {
        $response = $request->all();
        $this->storeMidWebhook($id, json_encode($response));
        $transaction = DB::table('transaction_session')->select("request_data")->where("transaction_id", $id)->first();
        if ($transaction == null || count($response) <= 0) {
            exit();
        }
        $input = json_decode($transaction->request_data, true);
        if (isset($response["reply_code"]) && $response["reply_code"] == "000") {
            $input["status"] = "1";
            $input["reason"] = "Your transaction has been processed successfully!";
        } else if (isset($response["reply_code"]) && $response["reply_code"] == "553") {
            $input["status"] = "2";
            $input["reason"] = isset($response["reply_desc"]) ? $response["reply_desc"] : "Your transaction is under process . Please wait for sometime!";
        } else {
            $input["status"] = "0";
            $input["reason"] = isset($response["reply_desc"]) ? $response["reply_desc"] : "Your transaction could not processed!";
        }
        $this->storeTransaction($input);
    }

    // * The status API 
    public function getStatus($mid, $sessionId)
    {
        $strConcat = $mid->merchant_no . $sessionId . $mid->hash;
        $shaStr = hash("sha256", $strConcat);
        $signature = base64_encode($shaStr);
        $url = self::STATUS_URL . "?Order=" . $sessionId . "&CompanyNum=" . $mid->merchant_no . "&signature=" . $signature;
        $response = Http::get($url)->json();
        return isset($response["data"]) && count($response["data"]) > 0 ? $response["data"][0] : null;

    }

    // * get the dob
    public function getDob(): string
    {
        $getDob = generateRandomDob();
        $dateArr = explode("-", $getDob);
        return $dateArr[0] . $dateArr[1] . $dateArr[2];
    }

    // * Convert response body to json
    public function responseBodyToJson($response)
    {
        $responseData = [];
        parse_str($response, $responseData);
        // Convert the array to JSON
        $jsonResponse = response()->json($responseData, Response::HTTP_OK);

        return $jsonResponse;
    }

    // * Pending transaction job 
    public function restorePendingTxn(Request $request)
    {
        try {
            if ($request->get("password") != "b5321a883e7e4318608a0184f3445545e3e56412") {
                abort(404);
            }
            $mid = checkAssignMID("40");
            HighiskPendingTxnJob::dispatch($mid, self::STATUS_URL);
            return response()->json(["status" => 200, "message" => "job added successfully!"]);
        } catch (\Exception $err) {
            return response()->json(["status" => 500, "message" => $err->getMessage()]);

        }


    }
}