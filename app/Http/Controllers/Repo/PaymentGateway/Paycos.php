<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\StoreTransaction;
use App\TransactionSession;
use Http;
use Log;

class Paycos extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://gateway-router.paycos.com';
    const STATUS_API = "https://business-api.paycos.com/api/v2/merchant/payment";


    public function checkout($input, $check_assign_mid)
    {
        try {
            $input['converted_amount'] = ceil(number_format((float) $input['converted_amount'], 2, '.', ''));
            $payload = [
                'product' => 'Pay by ' . $input['first_name'] . ' ' . $input['last_name'],
                'amount' => $input['converted_amount'] * 100,
                'currency' => $input['converted_currency'],
                // For payment_page_url - only use EUR or RUB
                'redirectSuccessUrl' => route('paycos-success', $input['session_id']),
                'redirectFailUrl' => route('paycos-fail', $input['session_id']),
                'locale' => 'en',
                'callback_url' => route('paycos-callback', $input['session_id']),
                'available_amounts_list' => [],
                // No need for below details, it is only required for the Host to host
                'card' => [
                    'pan' => $input['card_no'],
                    'expires' => $input['ccExpiryMonth'] . '/' . $input['ccExpiryYear'],
                    'holder' => $input['first_name'] . ' ' . $input['last_name'],
                    'cvv' => $input['cvvNumber']
                ],
                'customer' => [
                    'email' => $input['email'],
                    'address' => $input['address'],
                    'ip' => $input['ip_address'],
                    'phone' => $input['phone_no'],
                    "client_id" => $input["order_id"]
                ]
            ];

            $response = Http::withHeaders([
                "Content-Type" => "application/json",
                "Authorization" => 'Bearer ' . $check_assign_mid->merchant_key
            ])->post(self::BASE_URL . '/api/v1/init/pay', $payload)->json();

            $payload["card"]["pan"] = cardMasking($payload["card"]["pan"]);
            $payload["card"]["cvv"] = "XXX";
            $this->storeMidPayload($input["session_id"], json_encode($payload));

            // update session data
            if (isset($response['token'])) {
                $input['gateway_id'] = $response['token'] ?? null;
                $this->updateGatewayResponseData($input, $response);
            }

            if (!empty($response['success']) && $response['success'] == true && !empty($response['payment_page_url']['methods']["ob_card"])) {
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                    'redirect_3ds_url' => $response['payment_page_url']['methods']["ob_card"],
                ];
            }
            return [
                'status' => '0',
                'reason' => (isset($response['errors']) ? reset($response['errors']) : 'Your transaction could not processed.'),
                'order_id' => $input['order_id'],
            ];

        } catch (\Exception $e) {

            Log::info([
                'paycos-exception' => $e->getMessage()
            ]);
            return [
                'status' => '0',
                'reason' => $e->getMessage(),
                // 'Your transaction could not processed.',
                'order_id' => $input['order_id'],
            ];

        }
    }

    public function callback(Request $request, $id)
    {
        $body = $request->all();
        Log::info([
            'paycos-callback' => $body,
            'id' => $id
        ]);

        $transaction = TransactionSession::where('transaction_id', $id)
            ->select("id", "request_data", "payment_gateway_id", "gateway_id")
            ->orderBy('id', 'desc')
            ->first();
        if ($transaction == null) {
            return abort(404);
        }
        $input = json_decode($transaction['request_data'], true);
        $mid = checkAssignMID($transaction->payment_gateway_id);
        $txnStatusRes = $this->statusAPI($mid, $transaction->gateway_id ?? $body["token"]);
        $input['status'] = '2';
        $input['reason'] = 'Your transaction is in Pending.';
        if (isset($txnStatusRes["payment"]['status']) && $txnStatusRes["payment"]['status'] == "declined") {
            $input['status'] = '0';
            $input['reason'] = isset($body['declinationReason']) ? $body['declinationReason'] : 'Your transaction was Declined.';
        } else if (isset($txnStatusRes["payment"]['status']) && $txnStatusRes["payment"]['status'] == "approved") {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';
        }
        $this->storeTransaction($input);
        exit();
    }

    public function success(Request $request, $id)
    {
        $body = $request->all();

        $transaction = TransactionSession::where('transaction_id', $id)
            ->select("id", "rquest_data", "payment_gateway_id", "gateway_id")
            ->orderBy('id', 'desc')
            ->first();
        if ($transaction == null) {
            return abort(404);
        }
        $input = json_decode($transaction['request_data'], true);
        $mid = checkAssignMID($transaction->payment_gateway_id);
        $txnStatusRes = $this->statusAPI($mid, $transaction->gateway_id);
        if (isset($txnStatusRes["payment"]["status"]) && $txnStatusRes["payment"]["status"] == "approved") {
            $input["status"] = "1";
            $input["reason"] = "Transaction processed successfully!";
        } else {
            $input["status"] = "0";
            $input["reason"] = "Transaction could processed successfully!";
        }
        $this->updateGatewayResponseData($input, $txnStatusRes);
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function fail(Request $request, $id)
    {
        $body = $request->all();
        $transaction = TransactionSession::where('transaction_id', $id)
            ->select("id", "request_data", "payment_gateway_id", "gateway_id")
            ->orderBy('id', 'desc')
            ->first();
        if ($transaction == null) {
            return abort(404);
        }

        $input = json_decode($transaction['request_data'], true);
        $mid = checkAssignMID($transaction->payment_gateway_id);
        $txnStatusRes = $this->statusAPI($mid, $transaction->gateway_id);
        if (isset($txnStatusRes["payment"]) && $txnStatusRes["payment"]["status"] == "pending") {
            $input["status"] = "2";
            $input["reason"] = "Your transaction is under process. Please wait for sometime.";
        } else if (isset($txnStatusRes["payment"]["status"]) && $txnStatusRes["payment"]["status"] == "declined") {
            $input['status'] = '0';
            $input['reason'] = isset($txnStatusRes["payment"]["meta_data"]["errors"]) ? $txnStatusRes["payment"]["meta_data"]["errors"] : 'Your transaction could not processed.';
        } else {
            $input['status'] = '0';
            $input['reason'] = 'Your transaction could not processed.';
        }

        $this->updateGatewayResponseData($input, $txnStatusRes);
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    // * Status API
    public function statusAPI($mid, $token)
    {
        $response = Http::withHeaders(["Authorization" => 'Bearer ' . $mid->merchant_key])->get(self::STATUS_API . "/" . $token);
        return $response;
    }
}