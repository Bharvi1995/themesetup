<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class InfiPay extends Controller
{

    use StoreTransaction;

    const BASE_URL = "https://api.iptsys.net/api/deposit/create";
    const BANK_URL = "https://api.iptsys.net/api/deposit/bankcode";

    const STATUS_URL = "https://api.iptsys.net/api/deposit/check";

    public function checkout($input, $mid)
    {

        return [
            'status' => '7',
            'reason' => '3DS link generated successful, please redirect.',
            'redirect_3ds_url' => route('infipay-bank-select', [$input['session_id'], encrypt($mid->converted_currency)])
        ];

    }

    public function returnCallback(Request $request, $id)
    {
        $transaction = DB::table('transaction_session')->select('id', 'payment_gateway_id', "request_data")->where('transaction_id', $id)->first();
        if ($transaction == null) {
            return abort(404);
        }
        $input = json_decode($transaction->request_data, true);
        $mid = checkAssignMID($transaction->payment_gateway_id);
        $response = $this->statusAPI($mid, $id);
        $input["gateway_id"] = isset($response["data"]["refId"]) ? $response["data"]["refId"] : "1";
        if (isset($response["data"]) && $response["data"]["status"] == "SUCCESS") {
            $input["status"] = "1";
            $input["reason"] = "Your transaction has been processed successfully.";
        } else if (isset($response["data"]) && $response["data"]["status"] == "FAILED") {
            $input['status'] = '0';
            $input['reason'] = isset($response['data']['err']) ? $response["data"]["err"] : "Your transaction got declined.";
        } else if (isset($response["data"]) && $response["data"]["status"] == "PROCESSING") {
            $input['status'] = '2';
            $input['reason'] = isset($response["data"]['err']) ? $response["data"]["err"] : "Your transaction is under process. please wait for sometime.";
        } else if (isset($response["data"]) && $response["data"]["status"] == "HOLD") {
            $input['status'] = '2';
            $input['reason'] = isset($response["data"]['err']) ? $response["data"]["err"] : "Your transaction is under process. please wait for sometime.";
        } else {
            $input['status'] = '0';
            $input['reason'] = isset($response["data"]['err']) ? $response["data"]["err"] : "Your transaction got declined.";
        }

        $this->updateGatewayResponseData($input, $response);
        $this->storeTransaction($input);

        // convert response in query string

        $store_transaction_link = $this->getRedirectLink($input);

        return redirect($store_transaction_link);

    }

    // * Bank View
    public function selectBank(Request $request, $id, $currency)
    {
        try {
            $midCurrency = decrypt($currency);
            $banksAPI = Http::get(self::BANK_URL . '?currency=' . $midCurrency)->json();
            $banks = $banksAPI["data"];
            if (empty($banks)) {
                Log::info(["infipay-bank-api-error" => "Did not got response from infipay bank api"]);
                $this->redirectUserBackOnError($id);
            }
            $paymentOption = config('infipay.' . $midCurrency . "Gateway");

            return view('gateway.infipay.select_bank', compact('banks', 'paymentOption', 'id'));

        } catch (\Exception $err) {
            Log::error(["infipay-selectBank-error" => $err->getMessage()]);
            $this->redirectUserBackOnError($id);
        }

    }


    // * store the bank 
    public function selectBankStore(Request $request)
    {
        $requestPayload = $request->validate([
            "session_id" => 'required',
            "gateway" => 'required',
            "bank" => function ($attribute, $value, $fail) use ($request) {
                $gateway = $request->input('gateway');
                if (($gateway === 'BT' || $gateway === 'IB') && empty($value)) {
                    $fail("The $attribute field is required.");
                }
            },
        ]);
        try {
            $transaction = DB::table('transaction_session')->select("id", "payment_gateway_id", "request_data")->where('transaction_id', $requestPayload["session_id"])->first();
            $mid = checkAssignMID($transaction->payment_gateway_id);
            $input = json_decode($transaction->request_data, true);

            $input["converted_amount"] = (string) ceil($input['converted_amount']);
            $token = hash_hmac('sha256', $mid->cid . '|' . $input["session_id"] . "|" . $requestPayload['gateway'] . "|" . $input["converted_amount"], $mid->key);

            $payload = [
                'CID' => $mid->cid,
                "refIdPartner" => $input["session_id"],
                "gateway" => $requestPayload['gateway'],
                "bank" => $requestPayload['bank'],
                "amount" => $input["converted_amount"],
                "currency" => $input['converted_currency'],
                "redirectUrl" => route('infipay-return', [$input["session_id"]]),
                'token' => $token
            ];

            // * Hit the API
            $response = Http::post(self::BASE_URL, $payload)->json();
            Log::info(["infipay" . $mid->converted_currency . "-request-payload" => $payload, "infipay" . $mid->converted_currency . "-response" => $response]);
            $input["gateway_id"] = isset($response["data"]["refId"]) ? $response["data"]["refId"] : "1";
            $this->updateGatewayResponseData($input, $response);
            if (empty($response)) {
                $input["status"] = "0";
                $input["reason"] = "We are facing temporary issue from the bank side. Please contact us for more detail.";
            } else if ($response["errCode"] == "000" && isset($response["data"]['payment']["url"])) {
                // * Redirect user to bank page
                return redirect($response["data"]['payment']["url"]);
            } else if ($response["errCode"] == "010" || $response["errCode"] == "012") {
                $input["status"] = "0";
                $input["reason"] = "Gateway is under maintenance.please contact us for more info.";
            } else {
                $input["status"] = "0";
                $input["reason"] = "Your transaction could not processed.";
            }
            $this->updateGatewayResponseData($input, $response);
            $this->storeTransaction($input);
            // convert response in query string

            $store_transaction_link = $this->getRedirectLink($input);

            return redirect($store_transaction_link);

        } catch (\Exception $err) {
            Log::error(["infipay-VND-error" => $err->getMessage()]);
            $this->redirectUserBackOnError($requestPayload["session_id"]);
        }
    }


    // * InfiPay Webhook
    public function webhook(Request $request, $id)
    {
        $response = $request->all();
        Log::info(["infipay-webhook-callback" => json_encode($response)]);
        if (empty($response) || !isset($response['refIdPartner'])) {
            abort(404);
        }
        $transactionSession = DB::table('transaction_session')->where('transaction_id', $response['refIdPartner'])->first();
        if ($transactionSession == null) {
            abort(404);
        }
        $input = json_decode($transactionSession->request_data, true);
        if ($response['status'] != null && $response['status'] == 'SUCCESS') {
            $input['status'] = '1';
            $input['reason'] = "Your transaction has been processed successfully.";
        } else if ($response['status'] != null && $response['status'] == 'FAILED') {
            $input['status'] = '0';
            $input['reason'] = isset($response['err']) ? $response["err"] : "Your transaction got declined.";
        } else if (isset($response["status"]) && $response["status"] == "PROCESSING") {
            $input['status'] = '2';
            $input['reason'] = isset($response['err']) ? $response["err"] : "Your transaction is under process. please wait for sometime.";
        } else if (isset($response["status"]) && $response["status"] == "HOLD") {
            $input['status'] = '2';
            $input['reason'] = isset($response['err']) ? $response["err"] : "Your transaction is under process. please wait for sometime.";
        } else {
            $input['status'] = '0';
            $input['reason'] = isset($response['err']) ? $response["err"] : "Your transaction got declined.";
        }

        $input['gateway_id'] = isset($response['refId']) ? $response['refId'] : "1";
        $this->updateGatewayResponseData($input, $request->all());
        $this->storeTransaction($input);
    }

    // * status API
    public function statusAPI($mid, $sessionId)
    {
        $token = hash_hmac('sha256', $mid->cid . '|' . $sessionId, $mid->key);
        $payload = [
            "CID" => $mid->cid,
            "refIdPartner" => $sessionId,
            "token" => $token
        ];
        $response = Http::post(self::STATUS_URL, $payload)->json();

        return $response;
    }

    // * Infipay error handler 
    public function redirectUserBackOnError($sessionId)
    {
        $transaction = DB::table('transaction_session')->select("request_data")->where('transaction_id', $sessionId)->first();
        $input = json_decode($transaction->request_data, true);
        $input["status"] = "0";
        $input["reason"] = "We are facing temporary issue from the bank side. Please contact us for more detail.";
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }


}