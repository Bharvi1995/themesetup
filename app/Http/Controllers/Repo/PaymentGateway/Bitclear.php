<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class Bitclear extends Controller
{

    use StoreTransaction;

    const BASE_URL = "https://api-sandbox.bitclear.li"; // Sandbox url
    // const BASE_URL = "https://api.bitclear.li"; // production url

    public function checkout($input, $mid)
    {
        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');

        $payload = [
            "priceAmount" => $input["converted_amount"],
            "priceCurrency" => $input["converted_currency"],
            "transferCurrency" => "BTC",
            "walletLabel" => config("app.name") . " Pay",
            "walletMessage" => $input["order_id"] . " transaction",
            "notificationUrl" => route("bitclear.notification", [$input["session_id"]]),
            "externalPaymentId" => $input["session_id"],
            "redirectUrls" => [
                "expired" => route('bitclear.expired', [$input["session_id"]]),
                "confirmed" => route("bitclear.confirmed", [$input["session_id"]]),
                "invalid" => route('bitclear.invalid', [$input["session_id"]]),
                "unconfirmed" => route('bitclear.unconfirmed', [$input["session_id"]]),
            ]
        ];

        $response = Http::withBasicAuth($mid->user_id, $mid->password)->post(self::BASE_URL . "/v3/payments", $payload)->json();
        Log::info(["bitclear-res" => $response, "authBasic" => [$mid->user_id, $mid->password]]);

        if ($response == null || empty($response)) {
            return [
                'status' => '0',
                'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                'order_id' => $input['order_id'],
            ];
        }

        $input["gateway_id"] = $response["paymentId"] ?? "1";
        $this->updateGatewayResponseData($input, $response);

        if (isset($response["paymentPageUrl"]) && $response["paymentPageUrl"] != "") {
            return [
                'status' => '7',
                'reason' => '3DS link generated successful, please redirect.',
                'redirect_3ds_url' => $response["paymentPageUrl"]
            ];
        } else {
            return [
                "status" => "0",
                "reason" => $response["message"] ?? "Transaction could not processed!"
            ];
        }

    }

    // * expired callback
    public function expired(Request $request, $id)
    {
        $response = $request->all();
        Log::info("expired callback =>" . $response);
    }

    // * confirmed callback
    public function confirmed(Request $request, $id)
    {
        $response = $request->all();
        Log::info("confirmed callback =>" . $response);
    }

    // * unConfirmed callback
    public function unConfirmed(Request $request, $id)
    {
        $response = $request->all();
        Log::info("unConfirmed callback =>" . $response);
    }

    // * invalid callback
    public function invalid(Request $request, $id)
    {
        $response = $request->all();
        Log::info("invalid callback =>" . $response);
    }

    // * notification 
    public function notification(Request $request, $id)
    {
        $response = $request->all();
        Log::info("notification callback =>" . $response);
    }
}