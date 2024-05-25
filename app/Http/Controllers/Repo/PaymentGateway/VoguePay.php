<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Jobs\CentPayTransactionRestoreJob;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VoguePay extends Controller
{

    use StoreTransaction;

    const BASE_URL = "https://sandbox.voguepay.com/api/dotransaction"; // Sandbox URL
    // const BASE_URL = "https://secure.voguepay.com/api/dotransaction"; // Live URL


    public function checkout($input, $midDetails)
    {
        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');
        $payload = [
            "mid" => $midDetails->merchant_id,
            "serverkey" => $midDetails->key,
            "client_ip" => $input["ip_address"],
            "domainname" => "",
            "fullname" => $input["first_name"] . " " . $input["last_name"],
            "email" => $input["email"],
            "phone" => $input["phone_no"],
            "order_id" => $input["session_id"],
            "countrycode" => $input["country"],
            "currencycode" => $input["converted_currency"],
            "amount" => $input["converted_amount"],
            "paymentmode" => "creditcard",
            "paymenttype" => "sale",
            "bill_address" => $input["address"],
            "bill_city" => $input["city"],
            "bill_state" => $input["state"],
            "bill_country" => $input["country"],
            "bill_postalcode" => $input["zip"],
            "cardnum" => $input["card_no"],
            "expiryyear" => $input["ccExpiryYear"],
            "expirymonth" => $input["ccExpiryMonth"],
            "cardcvv" => $input["cvvNumber"],
            "cardholder" => $input["first_name"] . " " . $input["last_name"],
            "cardpin" => "N/A",
            "response_url" => ""
        ];

        // * Hit the API
        $response = Http::post(self::BASE_URL, $payload)->json();
        Log::info(["voguePay-response" => $response]);
    }

}