<?php

namespace App\Http\Controllers\Repo\PaymentGateway;


use DB;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Http;

class Bitpace extends Controller
{

    use StoreTransaction;

    const BASE_URL = "https://api-sandbox.bitpace.com/";
    // const AUTH_URL = "https://api.bitpace.com/api/v1/"; // Production
    const AUTH_URL = "https://api-sandbox.bitpace.com/api/v1/"; // Sandbox

    public function checkout($input, $midDetails)
    {
        // * Get token 
        $token = $this->generateToken($midDetails);
        Log::info(["the-token-is" => $token]);

        if ($token == null) {
            return [
                'status' => '0',
                'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                'order_id' => $input['order_id'],
            ];
        }

        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');

        $payload = [
            "order_amount" => $input["converted_amount"],
            "currency" => $input["currency"],
            "merchant_name" => $input["first_name"] . " " . $input["last_name"],
            "description" => "Crypto Transaction " . $input["order_id"],
            "ip_address" => $input["ip_address"],
            "customer" => [
                "reference_id" => $input["session_id"],
                "first_name" => $input["first_name"],
                "last_name" => $input["last_name"],
                "email" => $input["email"],
            ],
            "return_url" => route("bitpace.callback", [$input["session_id"]]),
            "failure_url" => route("bitpace.error.callback", [$input["session_id"]]),
        ];

        $response = Http::withHeaders(["Authorization" => $token])->post(self::BASE_URL . "fixed-deposit", $payload)->json();

        Log::info(["bitpace-response" => $response]);

        return [
            "status" => "0",
            "reason" => "Testing phase one."
        ];
    }

    // * Generate the Auth Token
    public function generateToken($mid)
    {
        $url = self::AUTH_URL . "auth/token";
        $response = Http::post($url, ["merchant_code" => $mid->merchant_code, "password" => $mid->password])->json();
        Log::info(["bitpace-auth-token-res" => $response]);

        if ($response != null && isset($response["code"]) == "00" && isset($response["data"]["token"])) {
            return $response["data"]["token"];
        }
        return null;

    }

    // * callback method
    public function callback(Request $request, $id)
    {
        $response = $request->all();
        Log::info(["bitpace-callback" => $response]);
    }

    // * Error callback method
    public function errorCallback(Request $request, $id)
    {
        $response = $request->all();
        Log::info(["bitpace-error-callback" => $response]);
    }
}