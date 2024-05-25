<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use Http;
use App\MIDDetail;
use Log;


class WinopayCrypto extends Controller
{

    use StoreTransaction;

    // const AUTH_TOKEN_URL = "https://auth.winopay.com/v1/token"; // Live End point
    const AUTH_TOKEN_URL = "https://sandbox.auth.winopay.com/v1/token"; // Test End point

    const BASE_URL = "https://sandbox.collections.winopay.com/v1/request-to-pay"; // Test Base URL
    // const  BASE_URL = "https://collections.winopay.com/v1/request-to-pay"; // Live  Base URL

    public function checkout($input, $mid)
    {
        return [
            "status" => "5",
            "reason" => "Greetings, as notified earlier, the bank has been advised to switch off the current mids we are using , we will be migrating you to new Mids , you can halt traffic for a while as we make this migration."
        ];
        $tokenResponse = $this->generateAuthToken($mid);
        if (!empty($tokenResponse) && isset($tokenResponse["data"])) {
            $input['converted_amount'] = (float) number_format($input['converted_amount'], 2, '.', '');
            $payload = [
                "currency" => $input["converted_currency"],
                "amount" => $input["converted_amount"],
                "payment_method" => "CRYPTO",
                "provider" => "btc_usd",
                "merchant_reference" => $input["session_id"],
                "narration" => "testpay Transaction",
                "account_name" => $input["first_name"] . " " . $input["last_name"],
                "account_number" => "2567041111111",
                "account_email" => $input["email"],
                // "redirect_url" => route('winopay.return', [$input["session_id"]]),
                "redirect_url" => "https://testpay.com",
            ];

            $response = Http::withHeaders(["Content-Type" => 'application/json', "Accept" => "application/json", "Authorization" => $tokenResponse["data"]["token"]])->post(self::BASE_URL, $payload)->json();

            Log::info(["winopay-crypto-error" => $response]);
            // * Store the request payload
            $this->storeMidPayload($input["session_id"], json_encode($payload));

            if (empty($response)) {
                return [
                    "status" => "0",
                    "reason" => "We are facing temporary issue from the bank side. Please contact us for more detail."
                ];
            } else if (isset($response["code"]) && $response["code"] == "200" && $response["status"] == "accepted") {
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successful, please redirect.',
                    'redirect_3ds_url' => $response["payment_url"]
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

    // * to generate the auth token
    public function generateAuthToken($mid)
    {
        $response = Http::withHeaders(["Content-Type" => 'application/json', "Accept" => "application/json", "Secret-Key" => $mid->secret_key])->post(self::AUTH_TOKEN_URL, ["api_key" => $mid->api_key])->json();
        Log::info(["winopay-auth-token-res" => $response]);
        return $response;
    }



}