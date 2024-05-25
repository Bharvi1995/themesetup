<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Jobs\LeonePayPendingTxnJob;
use App\Traits\StoreTransaction;
use Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class LeonePay extends Controller
{
    use StoreTransaction;
    const BASE_URL = 'https://uspaymentz.com/api/payment/transact';
    const STATUS_URL = "https://pos.leonepay.com/api/payment/status";

    public function checkout($input, $check_assign_mid)
    {
        $card_type = "VISA";
        if ($input["card_type"] == "3") {
            $card_type = "MASTERCARD";
        }
        $country_code = \Config::get('phonecode.' . $input["country"]);
        $phone_no = trim($input["phone_no"]);
        $phone_no = str_replace(' ', '', $phone_no);
        $phone_no = str_replace('+', '', $phone_no);
        if (strlen($phone_no) > 12) {
            $phone_no = substr($phone_no, -12);
        } elseif (strlen($phone_no) <= 9) {

            if (isset($input["country_code"]) && !empty($input["country_code"])) {
                $country_code = $input["country_code"];
            }
            $phone_no = $country_code . "" . $phone_no;
        }
        if (strlen($phone_no) <= 9) {
            $phone_no = $country_code . $phone_no;
        }
        $phone_no = str_replace(' ', '', $phone_no);
        $phone_no = str_replace('+', '', $phone_no);
        $payload = [
            "apiKey" => $check_assign_mid->api_key,
            "firstName" => $input["first_name"],
            "lastName" => $input["last_name"],
            'email' => $input['email'],
            "phone" => $phone_no,
            "address" => $input["address"],
            "city" => $input["city"],
            "state" => $input["state"],
            "country" => $input["country"],
            "pincode" => $input["zip"],
            "amount" => $input['converted_amount'],
            "currency" => $input["converted_currency"],
            "cardType" => $card_type,
            "cardName" => $input["first_name"] . " " . $input["last_name"],
            "clientIP" => $input['ip_address'],
            "cardNumber" => $input["card_no"],
            "cardExpMonth" => $input["ccExpiryMonth"],
            "cardExpYear" => $input["ccExpiryYear"],
            "cardCVV" => $input["cvvNumber"],
            "orderID" => $input["order_id"],
            "redirectURL" => route("leonepay.redirect", $input["session_id"]),
            "webhookURL" => route("leonepay.webhook", $input["session_id"]),
        ];

        $response = Http::withHeaders(["Content-Type" => "application/json", "authToken" => $check_assign_mid->auth_token])->post(self::BASE_URL, $payload)->json();

        // * Store mid payload
        $payload["cardNumber"] = cardMasking($payload["cardNumber"]);
        $payload["cardCVV"] = "XXX";
        $this->storeMidPayload($input["session_id"], json_encode($payload));


        // Log::info(["leonepay-response" => $response]);

        if (empty($response) || $response == null) {
            return [
                "status" => "0",
                "reason" => "We are facing temporary issue from the bank side. Please contact us for more detail."
            ];
        }

        $input['gateway_id'] = isset($response["data"]["paymentId"]) ? $response["data"]["paymentId"] : "1";

        // update session-data
        $this->updateGatewayResponseData($input, $response);

        if (isset($response["success"]) && $response["success"] == false) {
            return [
                'status' => '0',
                'reason' => isset($response["status"]) ? trim($response["status"], "\n") : 'Your transaction could not processed.',
            ];
        }

        if (isset($response["status"]) && $response["status"] == "INITIATED" && isset($response["data"]["redirectUrl"]) && $response["data"]["redirectUrl"] != "") {
            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect.',
                'redirect_3ds_url' => $response["data"]["redirectUrl"],
            ];
        } elseif (isset($response["status"]) && $response["status"] == "APPROVED") {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';

        } elseif (isset($response["status"]) && $response["status"] == "PENDING") {
            $input['status'] = '2';
            $input['reason'] = "Transaction is pending in acquirer system, please check after few minutes.";
        } elseif ($response["status"] == "ERROR" || $response["status"] == "DECLINED") {
            $input['status'] = '0';
            $input['reason'] = isset($response["data"]["gatewayResponse"]) && $response["data"]["gatewayResponse"] != "" ? $response["data"]["gatewayResponse"] : 'Your transaction could not processed.';
        } else {
            $input['status'] = '0';
            $input['reason'] = 'Your transaction could not processed.';
        }
        return $input;
    }

    public function redirect(Request $request, $session_id)
    {
        $request_data = $request->all();
        $transaction_session = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first();
        if (empty($transaction_session)) {
            abort(404);
        }
        $input = json_decode($transaction_session->request_data, true);
        if (isset($request_data["status"]) && $request_data["status"] == "APPROVED") {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';
        } elseif (isset($request_data["status"]) && ($request_data["status"] == "DECLINED" || $request_data["status"] == "ERROR")) {
            $input['status'] = '0';
            $input['reason'] = $request_data["message"] ?? 'Transaction authentication failed.';
        } else {
            $input['status'] = '2';
            $input['reason'] = 'Transaction pending for approval.';
        }
        // redirect back to $response_url
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function webhook(Request $request, $session_id)
    {
        $request_data = $request->all();
        $this->storeMidWebhook($session_id, json_encode($request_data));
        $transaction_session = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first();
        if (empty($transaction_session)) {
            abort(404);
        }
        $input = json_decode($transaction_session->request_data, true);
        if (isset($request_data["status"]) && $request_data["status"] == "APPROVED") {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';
        } elseif (isset($request_data["status"]) && ($request_data["status"] == "DECLINED" || $request_data["status"] == "ERROR")) {
            $input['status'] = '0';
            $input['reason'] = $request_data["reason"] ?? 'Transaction authentication failed.';
        }
        if (isset($input["status"])) {
            $this->storeTransaction($input);
        }
        exit();
    }

    // * Pending txn job
    public function pendingTxnJob(Request $request)
    {
        try {
            if ($request->get("password") != "T1i5j5883e7e4318608a0184f3445545e3e56490") {
                abort(404);
            }
            $mid = checkAssignMID("44");
            LeonePayPendingTxnJob::dispatch($mid, self::STATUS_URL);
            return response()->json(["status" => 200, "message" => "job added successfully!"]);
        } catch (\Exception $err) {
            return response()->json(["status" => 500, "message" => $err->getMessage()]);

        }
    }
}