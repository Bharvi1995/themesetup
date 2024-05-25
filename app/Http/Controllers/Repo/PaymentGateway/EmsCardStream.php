<?php
namespace App\Http\Controllers\Repo\PaymentGateway;


use DB;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Http;

class EmsCardStream extends Controller
{

    use StoreTransaction;

    const BASE_URL = "https://gateway.cardstream.com/direct/";

    public function checkout($input, $midDetails)
    {


        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');
        $ccYear = explode("20", $input["ccExpiryYear"]);
        $payload = [
            "merchantID" => $midDetails->merchant_no,
            "action" => "SALE",
            "type" => 1,
            "currencyCode" => $input["converted_currency"],
            "countryCode" => $input["country"],
            "amount" => $input["converted_amount"],
            "paymentMethod" => "card",
            "cardNumber" => $input["card_no"],
            "cardExpiryMonth" => (int) $input["ccExpiryMonth"],
            "cardExpiryYear" => (int) $ccYear[1],
            "cardCVV" => $input["cvvNumber"],
            'customerName' => $input["first_name"] . " " . $input["last_name"],
            'customerEmail' => $input["email"],
            'customerAddress' => $input["address"],
            'customerPostCode' => $input["zip"],
            "orderRef" => $input["order_id"],
            'remoteAddress' => $_SERVER['REMOTE_ADDR'],
            'deviceIdentity' => $_SERVER['HTTP_USER_AGENT'],
            'deviceAcceptContent' => $_SERVER['HTTP_ACCEPT'],
            'deviceAcceptLanguage' => request()->header('Accept-Language') ?? "English",
            'threeDSOptions' => array(
                'paymentAccountAge' => '20190601',
                'paymentAccountAgeIndicator' => '05',
            ),
            "transactionUnique" => $input["session_id"],
            // "redirectURL" => route('ems.return', [$input["session_id"]]),
            'threeDSRedirectURL' => route('ems.return', [$input["session_id"]]),
            "callbackURL" => route('ems.callback', [$input["session_id"]])
        ];

        $payload["signature"] = $this->createSignature($payload, $midDetails->key);

        Log::info(["ems-payload" => $payload]);

        $response = Http::asForm()->post(self::BASE_URL, $payload)->body();

        $payload["cardNumber"] = cardMasking($payload["cardNumber"]);
        $payload["cardCVV"] = "XXX";
        $this->storeMidPayload($input["session_id"], json_encode($payload));
        // * Parse the query string response to an array
        $resArray = [];
        parse_str($response, $resArray);
        $input["gateway_id"] = isset($resArray["threeDSXID"]) ? $resArray["threeDSXID"] : "1";
        $this->updateGatewayResponseData($input, $resArray);

        Log::info(["the-ems-res" => $resArray]);
        if (empty($response) || $response == "" || $response == null) {
            return [
                'status' => '0',
                'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                'order_id' => $input['order_id'],
            ];
        }



        // * check both signature first
        $signature = null;
        if (isset($resArray["signature"])) {
            $signature = $resArray["signature"];
            unset($resArray["signature"]);
        }
        if (!$signature || $signature != $this->createSignature($resArray, $midDetails->key)) {
            return [
                "status" => "0",
                "reason" => "Transaction could not processed.signature not matched."
            ];
        }
        if (isset($resArray["responseCode"]) && $resArray["responseCode"] == "65802") {
            return [
                'status' => '7',
                'reason' => '3DS link generated successful, please redirect.',
                'redirect_3ds_url' => route('ems.form', [$input["session_id"]])
            ];
        }
        return [
            "status" => "0",
            "reason" => "Transaction could not processed."
        ];
    }

    function createSignature(array $data, $key)
    {
        // Sort by field name
        ksort($data);
        // Create the URL encoded signature string
        $ret = http_build_query($data, '', '&');
        // Normalise all line endings (CRNL|NLCR|NL|CR) to just NL (%0A)
        $ret = str_replace(array('%0D%0A', '%0A%0D', '%0D'), '%0A', $ret);
        // Hash the signature string and the key together
        return hash('SHA512', $ret . $key);
    }


    // * EMS form
    public function form(Request $request, $id)
    {
        $transaction = DB::table("transaction_session")->select("id", "request_data", "response_data", "payment_gateway_id")->where("transaction_id", $id)->first();
        if ($transaction == null) {
            abort(404);
        }

        $response = json_decode($transaction->response_data, true);
        // Log::info(["ems-form-response" => $response]);
        return view('gateway.ems.form', compact('response'));

    }

    // * return callback url
    public function return (Request $request, $id)
    {

        $response = $request->all();
        Log::info(["ems-return-callback" => $response]);
    }

    // * Callback url
    public function callback(Request $request, $id)
    {
        $response = $request->all();
        Log::info(["ems-callback" => $response, "id" => $id]);
    }
}