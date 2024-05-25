<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class TomiPay extends Controller
{
    use StoreTransaction;

    protected $transaction;

    // For Live URL just replace sandbox to gate

    const BASE_URL = 'https://gate.tomipay.net/payment/api/v2/sale/group/';
    const STATUS_URL = 'https://gate.tomipay.net/payment/api/v2/status/group/';
    public function __construct()
    {
        $this->transaction = new Transaction;
    }

    public function checkout($input, $check_assign_mid)
    {

        $input["converted_amount"] = number_format((float) $input['converted_amount'], 2, '.', '');
        $signString = '';
        $signString .= $check_assign_mid->groupId . $input['session_id'] . $input["converted_amount"] * 100 . $input['email'] . $check_assign_mid->control;
        $control = sha1($signString);

        try {
            $payload = [
                'client_orderid' => $input['session_id'],
                'order_desc' => config('app.name') . "Transaction",
                "amount" => $input['converted_amount'],
                "currency" => $input['converted_currency'],
                "address1" => $input["address"],
                "country" => $input['country'],
                "zip_code" => $input['zip'],
                "city" => $input['city'],
                "phone" => $input["phone_no"],
                "email" => $input['email'],
                "ipaddress" => $input['ip_address'],
                "control" => $control,
                "cvv2" => $input['cvvNumber'],
                "credit_card_number" => $input["card_no"],
                "card_printed_name" => $input["first_name"] . " " . $input["last_name"],
                "expire_month" => $input['ccExpiryMonth'],
                "expire_year" => $input["ccExpiryYear"],
                "first_name" => $input['first_name'],
                "last_name" => $input['last_name'],
                "state" => $input['country'],
                "redirect_url" => route('tomipay.redirect', [$input['session_id']]),
                "server_callback_url" => route('tomipay.webhook', [$input['session_id']]),
                // "server_callback_url" => "https://webhook.site/14414f53-7cdd-4b27-b28a-fbc478f5485d"
            ];

            // Adding group id in Attitudepay end point
            $request_url = self::BASE_URL . $check_assign_mid->groupId;
            $response = $this->sendRequest($request_url, $payload);
            // * Store mid payload 
            $payload["credit_card_number"] = cardMasking($payload["credit_card_number"]);
            $payload["cvv2"] = "XXX";
            $this->storeMidPayload($input["session_id"], json_encode($payload));

            if (!isset($response['paynet-order-id'])) {
                return [
                    'status' => '0',
                    'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                    'order_id' => $input['order_id'],
                ];
            }
            $paynetId = explode("\n", $response['paynet-order-id']);
            $input['gateway_id'] = isset($paynetId[0]) ? $paynetId[0] : "1";

            // * Create the status API url
            $statusShaString = $check_assign_mid->login . $input['order_id'] . $input['gateway_id'] . $check_assign_mid->control;
            $statusPayload = [
                'login' => $check_assign_mid->login,
                'client_orderid' => $input['order_id'],
                "orderid" => $input['gateway_id'],
                "control" => sha1($statusShaString),
            ];

            // * Get the transaction status from attitude api
            $status_request_url = self::STATUS_URL . $check_assign_mid->groupId;
            $statusResponse = $this->sendStatusRequest($status_request_url, $statusPayload);
            unset($statusResponse["html"]);
            $this->updateGatewayResponseData($input, $statusResponse);
            $statusCode = "";
            if (!empty($statusResponse)) {
                $statusCode = preg_replace("/\r|\n/", "", $statusResponse["status"]);
            }

            if (empty($statusResponse)) {
                return [
                    'status' => '0',
                    'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                    'gateway_id' => $input["gateway_id"]
                ];
            } else if (isset($statusResponse['redirect-to'])) {
                $getRedirectUrl = explode("\n", $statusResponse['redirect-to']);
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successful, please redirect.',
                    'redirect_3ds_url' => $getRedirectUrl[0]
                ];
            } else if (isset($statusResponse['status']) && $statusCode == "approved") {
                return [
                    "status" => "1",
                    "reason" => "Transaction processed successfully!",
                    'gateway_id' => $input["gateway_id"]
                ];
            } else if (isset($statusResponse['status']) && ($statusCode == "declined" || $statusCode == "error" || $statusCode == "filtered" || $statusCode == "unknown")) {
                return [
                    'status' => '0',
                    'reason' => isset($statusResponse['error-message']) ? preg_replace("/\r|\n/", "", $statusResponse["error-message"]) : "Your transaction could not processed.",
                    'gateway_id' => $input["gateway_id"]
                ];
            } else {
                return [
                    'status' => '0',
                    'reason' => "Your transaction could not processed.",
                    'gateway_id' => $input["gateway_id"]
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => '0',
                'reason' => $e->getMessage(),
                // 'Your transaction could not processed.',
                'gateway_id' => $input["gateway_id"]
            ];
        }
    }



    // Attitude Payment Send request
    function sendRequest($url, array $requestFields)
    {
        $curl = curl_init($url);

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_HEADER => 0,
                CURLOPT_USERAGENT => 'AltitudePay-Client/1.0',
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_POST => 1,
                CURLOPT_RETURNTRANSFER => 1
            )
        );

        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($requestFields));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error_message = 'Error occurred: ' . curl_error($curl);
            $error_code = curl_errno($curl);
        } elseif (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200) {
            $error_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error_message = "Error occurred. HTTP code: '{$error_code}'";
        }

        curl_close($curl);

        if (!empty($error_message)) {
            throw new RuntimeException($error_message, $error_code);
        }

        if (empty($response)) {
            throw new RuntimeException('Host response is empty');
        }

        $responseFields = array();

        parse_str($response, $responseFields);

        return $responseFields;
    }

    // Attitude Payment Status request
    function sendStatusRequest(string $url, array $requestFields)
    {
        $curl = curl_init($url);

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_HEADER => 0,
                CURLOPT_USERAGENT => 'AltitudePay-Client/1.0',
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_POST => 1,
                CURLOPT_RETURNTRANSFER => 1
            )
        );

        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($requestFields));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error_message = 'Error occurred: ' . curl_error($curl);
            $error_code = curl_errno($curl);
        } elseif (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200) {
            $error_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error_message = "Error occurred. HTTP code: '{$error_code}'";
        }

        curl_close($curl);

        if (!empty($error_message)) {
            throw new RuntimeException($error_message, $error_code);
        }

        if (empty($response)) {
            throw new RuntimeException('Host response is empty');
        }

        $responseFields = array();

        parse_str($response, $responseFields);
        unset($responseFields["html"]);


        if (isset($responseFields['redirect-to'])) {
            return $responseFields;
        } else {
            if (isset($responseFields['status']) && (preg_replace("/\r|\n/", "", $responseFields["status"]) == "processing")) {
                return $this->sendStatusRequest($url, $requestFields);
            } else {
                return $responseFields;
            }
        }
    }

    public function redirect(Request $request, $sessionId)
    {
        $response = $request->all();
        Log::info([
            'tomipay-request-redirect' => json_encode($response),
        ]);
        $transaction_session = DB::table('transaction_session')
            ->select("request_data")
            ->where('transaction_id', $sessionId)
            ->first();
        if ($transaction_session == null) {
            return abort(404);
        }

        $input = json_decode($transaction_session->request_data, true);

        if ($response['status'] != null && $response['status'] == 'approved') {
            $input['status'] = '1';
            $input['reason'] = "Your transaction has been processed successfully.";
        } else if ($response['status'] != null && $response['status'] == 'declined') {
            $input['status'] = '0';
            $input['reason'] = $response['error_message'];
        } else if ($response['status'] != null && $response['status'] == 'processing') {
            $input['status'] = '2';
            $input['reason'] = "Your transaction is under process. please wait for sometime.";
        } else {
            $input['status'] = '0';
            $input['reason'] = isset($response['error_message']) ? $response['error_message'] : "Your transaction got declined.";
        }

        // $input['gateway_id'] = isset($response['orderid']) ? $response['orderid'] : "1";
        $this->updateGatewayResponseData($input, $request->all());
        $this->storeTransaction($input);

        // convert response in query string

        $store_transaction_link = $this->getRedirectLink($input);

        return redirect($store_transaction_link);
    }

    // The webhook callback URL 
    public function webhook(Request $request, $sessionId)
    {
        $response = $request->all();

        // * Update the webhook response
        $this->storeMidWebhook($sessionId, json_encode($response));
        $transactionSession = DB::table('transaction_session')
            ->select("request_data")
            ->where('transaction_id', $sessionId)->first();
        if ($transactionSession == null) {
            abort(404);
        }

        // * Store the webhook resonse
        $input = json_decode($transactionSession->request_data, true);

        if ($response['status'] != null && $response['status'] == 'approved') {
            $input['status'] = '1';
            $input['reason'] = "Your transaction has been processed successfully.";
        } else if ($response['status'] != null && $response['status'] == 'declined') {
            $input['status'] = '0';
            $input['reason'] = $response['error_message'];
        } else if ($response['status'] != null && $response['status'] == 'processing') {
            $input['status'] = '2';
            $input['reason'] = "Your transaction is under process. please wait for sometime.";
        } else {
            $input['status'] = '0';
            $input['reason'] = isset($response['error_message']) ? $response['error_message'] : "Your transaction got declined.";
        }

        $input['gateway_id'] = isset($response['processor-tx-id']) ? $response['processor-tx-id'] : $input["gateway_id"];
        $this->storeTransaction($input);
    }
}