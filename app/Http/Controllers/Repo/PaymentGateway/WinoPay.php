<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use Http;
use Log;
use Storage;
use Illuminate\Support\Facades\Response;


class WinoPay extends Controller
{

    use StoreTransaction;

    const AUTH_TOKEN_URL = "https://auth.winopay.com/v1/token"; // Live End point
    // const AUTH_TOKEN_URL = "https://sandbox.auth.winopay.com/v1/token"; // Test End point

    // const BASE_URL = "https://sandbox.collections.winopay.com/v1/request-to-pay"; // Test Base URL
    const BASE_URL = "https://collections.winopay.com/v1/request-to-pay"; // Live  Base URL

    // * Status API URL 
    // const STATUS_API_URL = "https://sandbox.transactions.winopay.com/v1/send-callback"; // Test URL
    const STATUS_API_URL = "https://transactions.winopay.com/v1/send-callback"; // Live URL

    const REFUND_URL = "https://collections.winopay.com/v1/refund";

    const WEBHOOK_HASH = "EhmHzdY4WjRgvsti9bOJA9SvW";

    const PUBLIC_KEY = "-----BEGIN PUBLIC KEY-----
MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEA0S96xJ0hJFzQesbs/AI3
dlV7XMr2HLnuWhgYTn7MUOsY7S3aA1jpkmVuvZDUhnOB7UkzbWJFh6zd2S3J6JZH
N9JXWt+XJzQ4Am+gUulR8F50IhqSKQUa7izrekOLTu83QgVoc+naSHcwDQLvtGRn
hU/QNZXX7Zey5KVFpMpep9rnFMtPInmAHvGC4uPPpyRv6/P+zBTfdBhvYQDWCNVo
NYAEagMixeoQZ7l3Rv7MNbJu3BS41cKnItlwplRUuLks6sTjHjfCvnaKz7pzbYrL
upf/2XBBsq4y/e/nLYxGyHdbFd+pjEPMoAg7oyJ9vhTb/w8qzl2EHrjJcp6QYt8l
VTzpxMr7MbV/fz7lTP0NtWEgrbSgH+p3IYuiDcwUmmztpVGfpJBYMUyy2SKsP1Kg
Z5CfFpaGDE4JEU1Nl7sdwmdWtZaROvDR26NtyQIDqWePghhHMeEOcilYtObCMpeq
ca263d32NGu11359ZiV/0eq/QDaKoYGNLxP9JXEa/5uBXMSjtCsOUPuq91AOJgM6
jJ7VhEUto+ORk0fw+EFeXAYlFXXB9yk9mhN7zNtS+M4toEqwZpUmwsFOeXHxTA7d
uzvizBzI8fbSf+BAuoEMDpunvoNtNEcigktPeNcomPeRVEzJSLRnq8ARsHqUS5ey
gnIlCAyHV1vWWV5RQgmklF0CAwEAAQ==
-----END PUBLIC KEY-----";

    public function checkout($input, $mid)
    {
        return [
            "status" => "5",
            "reason" => "Greetings, as notified earlier, the bank has been advised to switch off the current mids we are using , we will be migrating you to new Mids , you can halt traffic for a while as we make this migration."
        ];

        $tokenResponse = $this->generateAuthToken($mid);
        $cardString = $this->getEncryptCard($input);
        if (!empty($tokenResponse) && isset($tokenResponse["data"])) {
            $input['converted_amount'] = (float) number_format($input['converted_amount'], 2, '.', '');
            $payload = [
                "currency" => $input["converted_currency"],
                "amount" => $input["converted_amount"],
                "payment_method" => "CARD",
                "provider" => "card_usd",
                "merchant_reference" => $input["session_id"],
                "narration" => "testpay Transaction " . $input["order_id"],
                "account_email" => $input["email"],
                "account_name" => $input["first_name"] . " " . $input["last_name"],
                "encrypted_card" => $cardString,
                // "redirect_url" => route('winopay.return', [$input["session_id"]]),
                "redirect_url" => route('winopay.return', [$input["session_id"]]),
            ];
            $response = Http::withHeaders(["Content-Type" => 'application/json', "Accept" => "application/json", "Authorization" => $tokenResponse["data"]["token"]])->post(self::BASE_URL, $payload)->json();
            // Log::info(["winopay-payload" => $payload, "winopay-response" => $response]);
            // * Store the request payload
            $this->storeMidPayload($input["session_id"], json_encode($payload));

            $input["gateway_id"] = isset($response["data"]["winopay_reference"]) ? $response["data"]["winopay_reference"] : "1";
            $this->updateGatewayResponseData($input, $response);

            if (empty($response)) {
                return [
                    "status" => "0",
                    "reason" => "We are facing temporary issue from the bank side. Please contact us for more detail."
                ];
            } else if (isset($response["code"]) && $response["code"] == "202" && $response["status"] == "accepted") {
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successful, please redirect.',
                    'redirect_3ds_url' => $response["data"]["payment_url"]
                ];
            } else {
                return [
                    'status' => '0',
                    'reason' => $response['message'] ?? "Your transaction could not processed.",
                    'order_id' => $input['order_id'],
                ];
            }
        }

        return [
            "status" => "0",
            "reason" => "We are facing temporary issue from the bank side. Please contact us for more detail."
        ];
    }

    // * redirect callback
    public function return (Request $request, $id)
    {
        $response = $request->all();
        $transaction = DB::table("transaction_session")->select("id", "request_data", "payment_gateway_id")->where("transaction_id", $id)->first();
        if ($transaction == null) {
            abort(404);
        }
        $input = json_decode($transaction->request_data, true);
        $txnRes = null;

        // * Hit Status API to check the response 
        if (isset($response["winopay_reference"]) && $response["winopay_reference"] != "") {
            $mid = checkAssignMID($transaction->payment_gateway_id);
            $txnRes = $this->statusAPI($mid, $response["winopay_reference"]);
            if ($txnRes != null && !empty($txnRes)) {
                $response["status"] = $txnRes["transaction_status"];
                $response["message"] = isset($txnRes["message"]) && $txnRes["message"] != "" ? $txnRes["message"] : "";
            }
        }

        // * Update the input status and response
        $updatedInput = $this->updateTransactionReasonAndStatus($input, $response);

        $this->updateGatewayResponseData($updatedInput, $txnRes ?? $response);
        $updatedInput["card_type"] = isset($input["card_type"]) && $input["card_type"] != "" ? $input["card_type"] : "2";
        $this->storeTransaction($updatedInput);

        // convert response in query string
        $store_transaction_link = $this->getRedirectLink($updatedInput);

        return redirect($store_transaction_link);
    }

    // * To listen webhook callback
    public function callback(Request $request)
    {
        $requestPayload = $request->all();
        $hash = $request->header("webhook-hash");
        // Log::info(["Winopay-webhook-response" => ["hash" => $hash, "data" => $requestPayload]]);
        if (isset($hash) && $hash == self::WEBHOOK_HASH && !empty($requestPayload)) {
            if (isset($requestPayload["merchant_reference"])) {
                $transaction = DB::table('transaction_session')->select("id", "request_data")->where("transaction_id", $requestPayload["merchant_reference"])->first();
                $this->storeMidWebhook($requestPayload["merchant_reference"], json_encode($requestPayload));
                if ($transaction == null) {
                    exit();
                }
                $input = json_decode($transaction->request_data, true);

                // * add the status property.
                $requestPayload["message"] = isset($requestPayload["message"]) && $requestPayload["message"] != "" ? $requestPayload["message"] : null;
                $requestPayload["status"] = $requestPayload["transaction_status"];
                // * Update the input status and response
                $updatedInput = $this->updateTransactionReasonAndStatus($input, $requestPayload);
                // Log::info(["winopay-webhook_input_res" => json_encode($updatedInput)]);
                $this->storeTransaction($updatedInput);
            }
            return Response::json(["status" => 200, "message" => "Webhook received."], 200);
        } else {
            return Response::json(["status" => 400, "message" => "Webhook received."], 400);
        }


    }

    // * to generate the auth token
    public function generateAuthToken($mid)
    {
        $response = Http::withHeaders(["Content-Type" => 'application/json', "Accept" => "application/json", "Secret-Key" => $mid->secret_key])->post(self::AUTH_TOKEN_URL, ["api_key" => $mid->api_key])->json();
        // Log::info(["token-response" => $response]);
        return $response;
    }


    // * Transaction Status API 
    public function statusAPI($mid, $id)
    {
        $tokenResponse = $this->generateAuthToken($mid);
        if (!empty($tokenResponse) && isset($tokenResponse["data"])) {
            $response = Http::withHeaders(["Content-Type" => 'application/json', "Accept" => "application/json", "Authorization" => $tokenResponse["data"]["token"]])->get(self::STATUS_API_URL . "/" . $id)->json();
            if (isset($response["code"]) && $response["code"] == 200) {
                return $response["data"]["payload"];
            }
            return null;
        } else {
            return null;
        }
    }

    // * Get the card json string 
    public function getEncryptCard(array $input): string
    {
        $expiryYear = explode("20", $input["ccExpiryYear"]);
        // Log::info(["the-card-wxpiry-year" => $expiryYear]);
        $cardInfo = [
            "full_name" => $input["first_name"] . " " . $input["last_name"],
            "card_number" => $input["card_no"],
            "expiry_month" => $input["ccExpiryMonth"],
            "expiry_year" => $expiryYear[1],
            "billing_address" => $input["address"],
            "billing_city" => $input["city"],
            "billing_zip" => $input["zip"],
            "billing_state" => $input["state"],
            "billing_country" => $input["country"],
            "cvv" => $input["cvvNumber"],
        ];

        $publicKey = openssl_get_publickey(self::PUBLIC_KEY);

        $ret = null;

        if (openssl_public_encrypt(json_encode($cardInfo), $result, $publicKey, OPENSSL_PKCS1_PADDING)) {
            // get the base64 encoded version of the encrypted string
            $ret = base64_encode('' . $result);
        }

        return $ret;

    }


    // * Update the transaction state and reason
    public function updateTransactionReasonAndStatus(array $input, array $response): array
    {
        $input["gateway_id"] = isset($response["winopay_reference"]) ? $response["winopay_reference"] : "1";

        if (isset($response["status"]) && $response["status"] == "FAILED") {
            $input['status'] = '0';
            $input['reason'] = $response["message"] != null && $response["message"] != "" ? $response["message"] : "Your transaction got declined.";
        } else if (isset($response["status"]) && $response["status"] == "COMPLETED") {
            $input['status'] = '1';
            $input['reason'] = $response["message"] != null && $response["message"] != "" ? $response["message"] : "Transaction processed successfully.";
        } else if (isset($response["status"]) && $response["status"] == "CANCELLED") {
            $input['status'] = '0';
            $input['reason'] = $response["message"] != null && $response["message"] != "" ? $response["message"] : "User cancelled the payment process.";
        } else if (isset($response["status"]) && $response["status"] == "PENDING") {
            $input['status'] = '2';
            $input['reason'] = $response["message"] != null && $response["message"] != "" ? $response["message"] : "Your transaction is under process . Please wait for sometime!";
        } else {
            $input['status'] = '0';
            $input['reason'] = $response["message"] != null && $response["message"] != "" ? $response["message"] : "Your transaction could not processed.";
        }

        return $input;
    }


    // * Refund transaction 
    public function refundForm(Request $request)
    {

        return view('gateway.winopay.refund');
    }

    public function refund(Request $request)
    {
        $payload = $request->validate([
            "order_id" => "required",
            "gateway_id" => "required"
        ]);

        $transaction = DB::table("transaction_session")->select("payment_gateway_id")->where("order_id", $payload["order_id"])->first();

        if ($transaction == null) {
            return back()->withInput()->with(["error" => "No Transaction found with entered order id."]);
        }
        $mid = checkAssignMID($transaction->payment_gateway_id);
        $tokenResponse = $this->generateAuthToken($mid);

        if (!empty($tokenResponse) && isset($tokenResponse["data"]["token"]) && $tokenResponse["data"]["token"] != "") {
            $payload = [
                "winopay_reference" => $payload["gateway_id"]
            ];

            $response = Http::withHeaders(["Content-Type" => "application/json", "Accept" => "application/json", "Authorization" => $tokenResponse["data"]["token"]])->post(self::REFUND_URL, $payload)->json();
            Log::info(["refund-api-res" => $response]);
            if (empty($response) || $response == null) {
                return back()->withInput()->with(["error" => "Did not got any response from AQ refund API."]);
            } else if ($response["code"] == 400 && $response["status"] == "error") {
                return back()->withInput()->with(["error" => "Refund request failed.please make sure you have passed correct gateway id."]);
            } else if ($response["code"] == 202 && $response["status"] == "accepted") {
                return back()->with(["success" => isset($response["message"]) && $response["message"] != "" ? $response["message"] : "Transaction marked as refund successfully!"]);
            } else {
                return back()->with(["error" => "Refund could not processed!"]);
            }
        }

        // Log::info(["winopay-refund-token-res" => $tokenResponse]);
        return back()->withInput()->with(["error" => "Getting some error while generating the token."]);

    }

}