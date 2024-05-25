<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CaresPay extends Controller
{

    use StoreTransaction;

    protected $transaction;

    const BASE_URL = "https://testurl.carespay.com:28081";


    public function __construct()
    {
        $this->transaction = new Transaction();
    }

    public function checkout($input, $check_assign_mid)
    {

        try {
            $input['converted_amount'] = number_format((float)$input['converted_amount'], 2, '.', '');
            $md5String = $check_assign_mid->merNo . $input["order_id"] . "1" . $input['converted_amount'] . route('carespay.redirect', [$input['order_id']]) . $check_assign_mid->key;
            $md5 = md5($md5String);

            $payload = [
                'merNo' => $check_assign_mid->merNo,
                "amount" => $input['converted_amount'],
                "billNo" => $input['order_id'],
                "currency" => "1",
                "language" => "EN",
                "tradeUrl" => "https://testpay.com",
                "firstName" => $input['first_name'],
                "lastName" => $input['last_name'],
                "country" => $input["country"],
                'state' => $input['state'], // if your country US then use only 2 letter state code.
                'city' => $input['city'],
                'zipCode' => $input['zip'],
                'ip' => $input['ip_address'],
                'email' => $input['email'],
                'phone' => $input['phone_no'],
                'address' => $input['address'],
                "shippingFirstName" => $input["first_name"],
                "shippingLastName" => $input["last_name"],
                "shippingCountry" => $input["country"],
                "shippingState" => $input["state"],
                "shippingCity" => $input["city"],
                "shippingAddress" => $input["address"],
                "shippingZipCode" => $input["zip"],
                "shippingEmail" => $input["email"],
                "shippingPhone" => $input["phone_no"],
                "cardNum" => $input["card_no"],
                "year" => $input["ccExpiryYear"],
                "month" => $input["ccExpiryMonth"],
                "cvv2" => $input['cvvNumber'],
                "productInfo" => "testpay IO transaction",
                "md5Info" => $md5,
                "notifyUrl" => route('carespay.webhook', [$input['order_id']]),
                "returnURL" => route('carespay.redirect', [$input['order_id']]),
            ];

            $request_url = self::BASE_URL . "/carespay/pay";
            $response = Http::asForm()->post($request_url, $payload)->json();

            Log::info([
                "CaresPay-response" => $response
            ]);

            $input['gateway_id'] = $response["orderNo"];
            $this->updateGatewayResponseData($input, $response);
            if (empty($response)) {
                return [
                    'status' => '0',
                    'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                    'order_id' => $input['order_id'],
                ];
            } else if ($response['code'] == "P0004") {
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successful, please redirect.',
                    'redirect_3ds_url' => $response['auth3DUrl']
                ];
            } else if ($response['code'] == 'P0002') {
                return [
                    'status' => '0',
                    'reason' => $response['message'],
                ];
            } else if ($response['code'] == 'P0001') {
                return [
                    'status' => '1',
                    'reason' => $response['message'],
                ];
            } else {
                return [
                    'status' => '0',
                    'reason' => isset($response['message']) ? $response['message'] : "Your transaction got declined.",
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => '0',
                'reason' => $e->getMessage(), // 'Your transaction could not processed.',
                'order_id' => $input['order_id']
            ];
        }
    }

    // * Redirect User URL
    public function redirect(Request $request, $orderId)
    {
        $payload = $request->all();
        Log::info(["CaresPay-redirect-callback" => json_encode($payload)]);
        $transaction_session = DB::table('transaction_session')
            ->where('order_id', $orderId)
            ->first();
        if ($transaction_session == null) {
            return abort(404);
        }
        $input = json_decode($transaction_session->request_data, true);

        if (isset($payload) && $payload['code'] != null && $payload['code'] == "P0001") {
            $input['status'] = '1';
            $input['reason'] = "Your transaction has been processed successfully.";
        } else if (isset($payload) && $payload['code'] != null && $payload['code'] == "P0002") {
            $input['status'] = '0';
            $input['reason'] = $payload['message'];
        } else if (isset($payload) && $payload['code'] != null && $payload['code'] == "P0004") {
            $input['status'] = '2';
            $input['reason'] = "Yet to get the final response from the bank. please wait for sometime.";
        } else {
            $input['status'] = '0';
            $input['reason'] = isset($payload['message']) ? $payload['message'] : "Your transaction got declined.";
        }

        $input['gateway_id'] = $payload['orderNo'];
        $this->updateGatewayResponseData($input, $payload);
        $this->storeTransaction($input);

        $store_transaction_link = $this->getRedirectLink($input);

        return redirect($store_transaction_link);
    }

    // * Webhoom Url
    public function webhook(Request $request, $orderId)
    {
        $payload = $request->all();

        Log::info([
            'CaresPay-webhook-response' => json_encode($payload),
        ]);

        $transactionSession = DB::table('transaction_session')->where('order_id', $orderId)->first();
        if ($transactionSession == null) {
            abort(404);
        }
        $input = json_decode($transactionSession->request_data, true);

        // Check the transaction status 
        if (isset($payload) && $payload['code'] != null && $payload['code'] == "P0001") {
            $input['status'] = '1';
            $input['reason'] = "Your transaction has been processed successfully.";
        } else if (isset($payload) && $payload['code'] != null && $payload['code'] == "P0002") {
            $input['status'] = '0';
            $input['reason'] = $payload['message'];
        } else if (isset($payload) && $payload['code'] != null && $payload['code'] == "P0004") {
            $input['status'] = '2';
            $input['reason'] = "Yet to get the final response from the bank. please wait for sometime.";
        } else {
            $input['status'] = '0';
            $input['reason'] = isset($payload['message']) ? $payload['message'] : "Your transaction got declined.";
        }

        $input['gateway_id'] = $payload['orderNo'];
        $this->updateGatewayResponseData($input, $payload);
        $this->storeTransaction($input);
    }
}
