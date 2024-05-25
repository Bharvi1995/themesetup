<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Http;

class PivotPay extends Controller
{
    use StoreTransaction;

    const BASE_URL = "https://dr-prod.pivotpayts.com/card-integration/api/v1";

    public function checkout($input, $mid)
    {

        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');
        $cardPayload = [
            'cardNumber' => $input["card_no"],
            'expiryYear' => $input["ccExpiryYear"],
            'tranAmount' => $input['converted_amount'],
            'currency' => $input['converted_currency'],
            'vendorPassword' => $mid->merchant_password,
            'phoneNumber' => $input["phone_no"],
            'cvv' => $input["cvvNumber"],
            'expiryMonth' => $input["ccExpiryMonth"],
        ];

        $encryptData = $this->encryptData($cardPayload, $mid);
        $payload = [
            "tranNarration" => "Money In " . $input["order_id"],
            "fullName" => $input["first_name"] . " " . $input["last_name"],
            "transaction" => $encryptData,
            "phoneNumber" => $input["phone_no"],
            "vendorCode" => $mid->merchant_code,
            "email" => $input["email"],
            "redirect_url" => route('pivot.callback', ["id" => $input["session_id"]])
        ];

        $response = Http::withBasicAuth($mid->api_username, $mid->api_password)->post(self::BASE_URL . "/cardTransactionApi", $payload)->json();
        // Log::info(["cardPayload" => $cardPayload, "response" => $response]);
        $this->storeMidPayload($input["session_id"], json_encode($payload));
        $input["gateway_id"] = $response["tranId"] ?? 1;
        $this->updateGatewayResponseData($input, $response);

        if ($response == null || empty($response)) {
            return [
                "status" => "0",
                "reason" => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                "order_id" => $input["order_id"]
            ];
        }

        if (isset($response["status"]) && $response["status"] == true && isset($response["mode"]) && $response["mode"] == "redirect") {
            return [
                'status' => '7',
                'reason' => '3DS link generated successful, please redirect.',
                'redirect_3ds_url' => $response["redirectUrl"]
            ];
        } else if (isset($response["status"]) && $response["status"] == true && isset($response["mode"]) && $response["mode"] == "avs_noauth") {
            // * Authorize Card Payment when mode is avs_noauth
            $verifyPayload = [
                "tranId" => $response["tranId"],
                "city" => $input["city"],
                "address" => $input["address"],
                "state" => $input["state"],
                "country" => $input["country"],
                "zipcode" => $input["zip"]
            ];

            $verifyResponse = Http::withBasicAuth($mid->api_username, $mid->api_password)->post(self::BASE_URL . "/authorizeCardPayment", $verifyPayload)->json();

            if ($verifyResponse == null || empty($verifyResponse)) {
                return [
                    "status" => "0",
                    "reason" => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                    "order_id" => $input["order_id"]
                ];
            }
            $this->updateGatewayResponseData($input, $verifyResponse);
            if (isset($verifyResponse["status"]) && $verifyResponse["status"] == false) {
                return [
                    "status" => "0",
                    "reason" => $verifyResponse["message"] ?? "Transaction could not processed"
                ];
            } else if (isset($verifyResponse["status"]) && $verifyResponse["status"] == true && isset($verifyResponse["mode"]) && $verifyResponse["mode"] == "otp") {
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successful, please redirect.',
                    'redirect_3ds_url' => route('pivot.otp', ["id" => $input["session_id"]])
                ];
            } else if (isset($verifyResponse["status"]) && $verifyResponse["status"] == true && isset($verifyResponse["mode"]) && $verifyResponse["mode"] == "redirect") {
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successful, please redirect.',
                    'redirect_3ds_url' => $verifyResponse["redirectUrl"]
                ];
            } else {
                return [
                    "status" => "0",
                    "reason" => $verifyResponse["message"] ?? "User cancelled the transaction process."
                ];
            }

        } else if (isset($response["status"]) && $response["status"] == true && isset($response["mode"]) && $response["mode"] == "pin") {
            return [
                'status' => '7',
                'reason' => '3DS link generated successful, please redirect.',
                'redirect_3ds_url' => route('pivot.pin', ["id" => $input["session_id"]])
            ];
        } else {
            return [
                "status" => "0",
                "reason" => $response["message"] ?? "Transaction could not processed.",
            ];
        }

    }

    public function otpPage($id)
    {
        $transaction = DB::table("transaction_session")->select("request_data", "gateway_id")->where("transaction_id", $id)->first();
        if ($transaction == null) {
            abort(404, "Page not found!");
        }
        $input = json_decode($transaction->request_data, true);
        $amount = $input["converted_amount"];
        $currency = $input["converted_currency"];
        return view('gateway.pivotpay.otp', compact('id', 'amount', 'currency'));

    }

    public function storeOtpPage(Request $request)
    {
        $payload = $request->validate([
            "id" => "required",
            "otp" => "required"
        ]);
        $transaction = DB::table("transaction_session")->select("request_data", "gateway_id", "payment_gateway_id")->where("transaction_id", $payload["id"])->first();
        if ($transaction == null) {
            abort(404, "Page not found!");
        }
        $input = json_decode($transaction->request_data, true);
        $mid = checkAssignMID($transaction->payment_gateway_id);
        $payload = [
            "tranId" => $transaction->gateway_id,
            "otp" => $payload["otp"]
        ];

        $response = Http::withBasicAuth($mid->api_username, $mid->api_password)->post(self::BASE_URL . "/validateCardPayment", $payload)->json();
        if ($response == null || empty($response)) {
            $input["status"] = "0";
            $input["reason"] = "We are facing temporary issue from the bank side. Please contact us for more detail.";
        } else if (isset($response["status"]) && $response["status"] == false) {
            $input["status"] = "0";
            $input["reason"] = $response["message"] ?? "Transaction could not processed.";
        } else if (isset($response["status"]) && $response["status"] == true) {
            $input["status"] = "1";
            $input["reason"] = "Transaction could not processed successfully!";
        } else {
            $input["status"] = "0";
            $input["reason"] = "User cancelled the transaction process.";
        }
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);

    }

    public function pinPage($id)
    {
        $transaction = DB::table("transaction_session")->select("request_data", "gateway_id")->where("transaction_id", $id)->first();
        if ($transaction == null) {
            abort(404, "Page not found!");
        }
        $input = json_decode($transaction->request_data, true);
        $amount = $input["converted_amount"];
        $currency = $input["converted_currency"];
        return view('gateway.pivotpay.pin', compact('id', 'amount', 'currency'));

    }

    public function storePinPage(Request $request)
    {
        $payload = $request->validate([
            "id" => "required",
            "otp" => "required"
        ]);
        $transaction = DB::table("transaction_session")->select("request_data", "gateway_id", "payment_gateway_id")->where("transaction_id", $payload["id"])->first();
        if ($transaction == null) {
            abort(404, "Page not found!");
        }

        $input = json_decode($transaction->request_data, true);
        $mid = checkAssignMID($transaction->payment_gateway_id);
        $payload = [
            "tranId" => $transaction->gateway_id,
            "otp" => $payload["otp"]
        ];

        $response = Http::withBasicAuth($mid->api_username, $mid->api_password)->post(self::BASE_URL . "/authorizeCardPayment", $payload)->json();

        if ($response == null || empty($response)) {
            $input["status"] = "0";
            $input["reason"] = "We are facing temporary issue from the bank side. Please contact us for more detail.";
        } else if (isset($response["status"]) && $response["status"] == true && isset($response["mode"]) && $response["mode"] == "otp") {
            return redirect()->route('pivot.otp', ["id" => $input["session_id"]]);
        } else if (isset($response["status"]) && $response["status"] == true && isset($response["mode"]) && $response["mode"] == "redirect") {
            return redirect($response["redirectUrl"]);
        } else if (isset($response["status"]) && $response["status"] == false) {
            $input["status"] = "0";
            $input["reason"] = $response["message"] ?? "Transaction could not processed.";
        } else {
            $input["status"] = "0";
            $input["reason"] = $response["message"] ?? "User cancelled the transaction process.";
        }

        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function callback(Request $request, $id)
    {
        $transaction = DB::table("transaction_session")->select("request_data", "gateway_id", "payment_gateway_id")->where("transaction_id", $id)->first();
        if ($transaction == null) {
            abort(404, "Page not found!");
        }
        $input = json_decode($transaction->request_data, true);
        $mid = checkAssignMID($transaction->payment_gateway_id);
        $response = $this->statusApi($mid, $transaction->gateway_id);

        if ($response == null || empty($response)) {
            $input["status"] = "0";
            $input["reason"] = "User cancelled the transaction process";
        } else if (isset($response["status"]) && $response["status"] == true && $response["tranStatus"] == "failed") {
            $input["status"] = "0";
            $input["reason"] = $response["message"] ?? "Transaction could not processed.";
        } else if (isset($response["status"]) && $response["status"] == true && $response["tranStatus"] == "success") {
            $input["status"] = "1";
            $input["reason"] = "Transaction processed successfully!";
        } else if (isset($response["status"]) && $response["status"] == false) {
            $input["status"] = "0";
            $input["reason"] = "User cancelled the transaction process";
        }

        $this->storeMidWebhook($input["session_id"], json_encode($response));
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function statusApi($mid, $gatewayId)
    {
        $response = Http::withBasicAuth($mid->api_username, $mid->api_password)->post(self::BASE_URL . "/verifyCardPayment", ["tranId" => $gatewayId])->json();
        return $response;
    }

    public function encryptData($payload, $mid)
    {
        $secretKeyHex = substr(bin2hex($mid->secret_key), 0, 32);
        $encryptionMethod = "AES-256-CBC";
        $iv = '9919777181192712';
        $encryptedData = json_encode(openssl_encrypt(json_encode($payload), $encryptionMethod, $secretKeyHex, 0, $iv));

        return $encryptedData;
    }
}