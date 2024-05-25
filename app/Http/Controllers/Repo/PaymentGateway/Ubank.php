<?php

namespace App\Http\Controllers\Repo\PaymentGateway;


use DB;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Http;

class Ubank extends Controller
{

    use StoreTransaction;

    const BASE_URL = "https://www.bankconnect.online/sandbox-bank-request";
    // const BASE_URL = "https://www.bankconnect.online";
    public function checkout($input, $mid)
    {

        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');

        $payload = [
            "merchantno" => $mid->merchant_id,
            "order_id" => $input["session_id"],
            "amount" => $input["converted_amount"],
            "pay_by" => "1",
            "email" => $input["email"],
            "mobile_no" => $input["phone_no"],
            "currency" => $input["converted_amount"],
            "fname" => $input["first_name"],
            "lname" => $input["last_name"],
            "state" => $input["state"],
            "city" => $input["city"],
            "address" => $input["address"],
            "pin" => $input["zip"],
            "payment_mode" => "TEST",
            "card_number" => $input["card_no"],
            "expiration_date" => $input["ccExpiryMonth"],
            "expiration_year" => $input["ccExpiryYear"],
            "security_code" => $input["cvvNumber"],
            // "return_url" => route("ubank.return", [$input["session_id"]]),
            // "callback_url" => route("ubank.callback", [$input["session_id"]]),
            "return_url" => "https://webhook.site/b1c92bd7-09ec-4fa4-8746-1cf92660f13e",
            "callback_url" => "https://webhook.site/b1c92bd7-09ec-4fa4-8746-1cf92660f13e",
        ];

        $payload["signature"] = $this->createHash($input, $mid, $payload["return_url"]);
        $response = Http::post(self::BASE_URL, $payload)->json();
        Log::info(["payload" => $payload, "response" => $response]);

        return [
            "status" => "0",
            "reason" => "Testing phase 1"
        ];
    }


    // * callback url
    public function callback(Request $request, $id)
    {
        $response = $request->all();
        Log::info(["ubank-callback" => $response]);
    }

    public function return (Request $request, $id)
    {
        $response = $request->all();
        Log::info(["ubank-return" => $response]);
    }

    // * Create Hash
    public function createHash($input, $mid, $returnUrl)
    {
        $str = $mid->secret_key . '@' . $input["session_id"] . '@' . $input['converted_amount'] . '@@@' . $input["phone_no"] . '@' . $input["email"] . '@@@@@@' . $returnUrl . '@@@@' . $mid->merchant_id;
        $hash = md5($str);
        return $hash;
    }
}