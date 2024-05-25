<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Jobs\CentPayTransactionRestoreJob;
use App\Traits\StoreTransaction;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Symoco extends Controller
{
    use StoreTransaction;

    const BASE_URL = "https://api.sandbox.symoco.com/v1/payments"; // testing url
    // const BASE_URL = "https://api.symoco.com/v1/payments"; // live URL

    public function checkout($input, $midDetails)
    {

        $cardArray = [
            "pan" => $input["card_no"],
            "cvv" => $input["cvvNumber"],
            "exp_month" => $input["ccExpiryMonth"],
            "exp_year" => $input["ccExpiryYear"]
        ];
        $encryptedCardString = encrypt(json_encode($cardArray));



        return [
            'status' => '7',
            'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
            'redirect_3ds_url' => route('symoco.initialPage', [
                $input["session_id"],
                $encryptedCardString
            ]),
        ];
    }

    // * 3ds Page 
    public function initialPage(Request $request, $id, $card)
    {
        return view('gateway.symoco.index', compact('id', 'card'));
    }

    // * send request payload to server
    public function authPage(Request $request)
    {
        $requestData = $request->only(["session_id", "card", "fingerprint"]);
        $transaction_session = DB::table('transaction_session')
            ->where('order_id', $requestData['session_id'])
            ->first();
        if ($transaction_session == null) {
            return abort(404);
        }
        $input = json_decode($transaction_session->request_data, true);

        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');
        $payload = [
            "orderId" => $input['session_id'],
            "description" => "testpay IO transaction",
            "autoConfirm" => false,
            "options" => [
                ""
            ],
            "returnUrl" => "https://result.domain.tld",
            "callbackUrl" => "https://callback.domain.tld",
            "customer" => [
                "accountId" => "df1a2c9e-bc2b-11ed-8332-811eecb5ec73",
                "fingerprint" => "30faf01ebf161c7c7673b776820b3517|1ea851ab5b0e311ed9c690828f8dc7c6|eyJEZXZpY2VGaW5nZXJwcmludCI6IjMwZmFmMDFlYmYxNjFjN2M3NjczYjc3NjgyMGIzNTE3In0=",
                "ip" => $input["ip_address"],
                "phone" => $input['phone_no'],
                "email" => $input['email'],
                "fullName" => $input["first_name"] . " " . $input["last_name"],
                "country" => $input["country"],
                "address" => $input["address"],
                "city" => $input["city"],
                "state" => $input["state"],
                "postalCode" => $input["zip"],
                "neighborhood" => $input["address"],
                // "birthdate" => "1960-02-25",
                // "browserData" => [
                //     "acceptHeader" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                //     "colorDepth" => 32,
                //     "language" => "en",
                //     "screenHeight" => 667,
                //     "screenWidth" => 375,
                //     "timezone" => -60,
                //     "userAgent" => "Mozilla/5.0 (iPhone; CPU iPhone OS 15_7_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.6.4 Mobile/15E148 Safari/604.1",
                //     "javaEnabled" => true,
                //     "windowHeight" => 850,
                //     "windowWidth" => 375
                // ]
            ],
            "amount" => [
                "value" => $input["converted_amount"],
                "currency" => $input["converted_currency"]
            ],
            "paymentData" => [
                "type" => "card",
                "object" => [
                    "encryptedCardData" => $requestData['card'],
                    "cardHolder" => $input["first_name"] . " " . $input["last_name"],
                    "additionalData" => [
                        "type" => "applepay",
                        "object" => [
                            "paymentToken" => "string",
                            "eci" => "st",
                            "cavv" => $input["cvvNumber"]
                        ]
                    ]
                ]
            ],
        ];

    }

    // * Symoco redirect
    public function redirect(Request $request, $id)
    {
        $payload = $request->all();
        $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', $id)
            ->first();
        if ($transaction_session == null) {
            return abort(404);
        }
    }

    // * When there is some issue in finger print generation
    public function fingerprintError(Request $request)
    {
        $payload = $request->only(["session_id", "card", "fingerprint"]);
        Log::error(["symoco-fingerprint-error" => json_encode($payload)]);
        $transaction_session = DB::table('transaction_session')->where('transaction_id', $payload['session_id'])->first();
        if ($transaction_session == null) {
            return abort(404);
        }

        $input = json_decode($transaction_session->request_data, true);
        $input["gateway_id"] = "1";
        $input['status'] = '0';
        $input['reason'] = "We are facing temporary issue from the bank side. Please contact us for more detail.";

        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }
}