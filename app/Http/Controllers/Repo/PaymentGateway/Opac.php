<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Http;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\Transaction;
use App\TransactionSession;
use Log;

class Opac extends Controller
{

    // const BASE_URL = 'https://api.sandbox.openacquiring.com'; //Test
    const BASE_URL = 'https://api.openacquiring.com'; //Live

    use StoreTransaction;

    // ================================================
    /* method : __construct
     * @param  :
     * @Description : Create a new controller instance.
     */// ==============================================
    public function __construct()
    {
        $this->transaction = new Transaction;
        $this->transactionSession = new TransactionSession;
    }

    public function checkout($input, $check_assign_mid)
    {

        $cardpayload = [
            "card_no" => $input["card_no"],
            "cvvNumber" => $input["cvvNumber"],
            "ccExpiryMonth" => $input["ccExpiryMonth"],
            "ccExpiryYear" => $input["ccExpiryYear"],
        ];
        $encrypteCard = encrypt($cardpayload);
        $this->storeMidPayload($input["session_id"], json_encode($encrypteCard));

        return [
            'status' => '7',
            'reason' => '3DS link generated successful, please redirect.',
            'redirect_3ds_url' => route('opac.browser_info', [$input["session_id"]])
        ];

    }

    public function browserInfo(Request $request, $id)
    {
        return view('gateway.opac.browser_info', compact('id'));
    }

    public function storeBrowserInfo(Request $request)
    {
        $payload = $request->only(["session_id", "browser_info"]);
        $transaction = DB::table("transaction_session")->select("request_data")->where("transaction_id", $payload["session_id"])->first();
        $input = json_decode($transaction->request_data, true);
        $browser_info = json_decode($payload["browser_info"], true);
        $browser_info["browser_ip"] = $input["ip_address"];
        $browser_info["browser_accept_header"] = $request->header('Accept');
        $browser_info["window_width"] = strval($browser_info["window_width"]);
        $browser_info["window_height"] = strval($browser_info["window_height"]);
        $browser_info["browser_color_depth"] = strval($browser_info["browser_color_depth"]);
        $browser_info["browser_screen_width"] = strval($browser_info["browser_screen_width"]);
        $browser_info["browser_screen_height"] = strval($browser_info["browser_screen_height"]);
        return $this->initiatTransaction($payload["session_id"], $browser_info);
    }

    public function initiatTransaction($sessionId, $browserInfo)
    {
        $transaction = DB::table("transaction_session")->select("id", "request_data", "payment_gateway_id", "mid_payload")->where("transaction_id", $sessionId)->first();
        $input = json_decode($transaction->request_data, true);
        $mid = checkAssignMID($transaction->payment_gateway_id);
        $decryptedData = json_decode($transaction->mid_payload, true);
        try {
            $ccDetails = decrypt($decryptedData);
        } catch (\Exception $err) {
            exit();
        }
        $payload = [
            'intent' => 'auth',
            'payer' => [
                'payment_type' => 'CC',
                'funding_instrument' => [
                    'credit_card' => [
                        "number" => $ccDetails['card_no'],
                        "expire_month" => $ccDetails['ccExpiryMonth'],
                        "expire_year" => $ccDetails['ccExpiryYear'],
                        "cvv2" => $ccDetails['cvvNumber'],
                        "name" => $input['first_name'] . " " . $input['last_name']
                    ]
                ],
                'payer_info' => [
                    "email" => $input['email'],
                    "name" => $input['first_name'] . " " . $input['last_name'],
                    "billing_address" => [
                        "line1" => $input['address'],
                        "city" => $input['city'],
                        "country_code" => $input['country'],
                        "postal_code" => $input['zip'],
                        "state" => $input['state'],
                        "phone" => [
                            "number" => $input['phone_no']
                        ]
                    ]
                ],
                "browser_info" => $browserInfo,
            ],
            "payee" => [
                "email" => "tech@testpay.com",
                "merchant_id" => $mid->merchant_id
            ],
            'transaction' => [
                "type" => "1",
                'amount' => [
                    'currency' => $input['converted_currency'],
                    'total' => $input["converted_amount"]
                ],
                'invoice_number' => $input['order_id'],
                "return_url" => route('opac.return', [$sessionId])
            ],
        ];

        $response = Http::withHeaders(["authorization" => "Basic " . base64_encode($mid->client_id . ':' . $mid->client_secret), "content-type" => "application/json"])->post(self::BASE_URL . '/v1/merchants/' . $mid->merchant_id . '/payment', $payload)->json();

        // * Update the mid payload
        $payload["payer"]["funding_instrument"]["credit_card"]["number"] = cardMasking($payload["payer"]["funding_instrument"]["credit_card"]["number"]);
        $payload["payer"]["funding_instrument"]["credit_card"]["cvv2"] = "XXX";
        $this->storeMidPayload($sessionId, json_encode($payload));

        Log::info(["opac-response" => $response]);

        if (isset($response)) {

            $input['gateway_id'] = isset($response['id']) ? $response["id"] : "1";

            if (isset($response['result']) && $response['result']['code'] == '0000') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was processed successfully.';
            } else if (isset($response['result']['redirect_url']) && $response['result']['redirect_url'] != "") {
                return redirect($response["result"]["redirect_url"]);
            } else {
                $input['status'] = '0';
                $input['reason'] = isset($response['result']["description"]) ? $response['result']["description"] : 'Transaction declined.';
            }
        } else {
            $input["gateway_id"] = "1";
            $input['status'] = '0';
            $input['reason'] = 'Transaction could not processed.';
        }

        // redirect back to $response_url
        $this->updateGatewayResponseData($input, $response);
        $this->storeTransaction($input);

        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }


    public function return (Request $request, $id)
    {
        Log::info(["opac-callback-res" => $request->all()]);

        $transaction = DB::table("transaction_sessions")->select("id", "request_data", "payment_gateway_id")->where("transaction_id", $id)->first();
        if ($transaction == null) {
            abort(404);
        }

    }

}