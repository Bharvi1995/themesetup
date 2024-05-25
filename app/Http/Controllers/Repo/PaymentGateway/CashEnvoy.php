<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CashEnvoy extends Controller
{
    use StoreTransaction;

    const BASE_URL = "https://www.cashenvoy.com/sandbox2/?cmd=cepay";

    public function checkout($input, $midDetails)
    {

        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');
        // generate request signature
        $data = $midDetails->key . $input["session_id"] . $input["converted_amount"];
        $signature = hash_hmac('sha256', $data, $midDetails->key, false);
        $payload = [
            "ce_merchantid" => $midDetails->merchant_id,
            "ce_amount" => $input["converted_amount"],
            "ce_transref" => $input["session_id"],
            "ce_customerid" => $input["email"],
            "ce_memo" => "testpay transaction",
            "ce_signature" => $signature,
            "ce_window" => "self",
            "ce_notifyurl" => route('cashenvoy.return', [$input["session_id"]]),
            "ce_ipnurl" => route('cashenvoy.webhook', [$input["session_id"]]),
        ];



        // * Hit the API
        $response = Http::asForm()->post(self::BASE_URL, $payload)->json();
        Log::info(["CashEnvoy-response" => $response]);

        return [
            "status" => "0",
            "reason" => "I am just testing guyes."
        ];
    }

    // * Return Callback 
    public function returnCallback(Request $request, $id)
    {
        $requestData = $request->all();
        Log::warning(["cashEnvoy-returnCallback" => $requestData]);
        dd($requestData);
    }

    // * Webhook Callback
    public function webhook(Request $request, $id)
    {
        $requestData = $request->all();
        Log::warning(["cashEnvoy-webhookCallback" => $requestData]);

        dd($requestData);
    }
}