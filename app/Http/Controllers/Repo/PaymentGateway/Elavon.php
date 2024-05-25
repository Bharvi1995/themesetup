<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;

class Elavon extends Controller
{
    use StoreTransaction;

    const BIN_URL = "https://gw.fraud.elavon.com/3ds2/lookup";
    public function checkout($input, $mid)
    {

        $binPayload = [
            "messageId" => $input["order_id"],
            "acctNumber" => $input["card_no"],
            "threeDSMethodNotificationURL" => "https://uat.gw.fraud.eu.elavonaws.com",
            "doBinLookup" => true,
            "clientStartProtocolVersion" => "2.1.0",
            "clientEndProtocolVersion" => "2.2.0",
        ];

        $response = Http::withHeaders(["Authorization" => base64_encode($mid->api_key)])->post(self::BIN_URL, $binPayload)->json();

        Log::info(["elavon-res" => $response]);
        return [
            "status" => "0",
            "reason" => "Testing phase 1"
        ];
    }
}