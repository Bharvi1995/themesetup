<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Payzentric extends Controller
{

    use StoreTransaction;

    const websiteUrl = "https://testpay.com";

    public function checkout($input, $midDetails)
    {
        // * Get the Auth Token
        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');
        $authTokenRes = $this->generateAuthToken($midDetails);
        if ($authTokenRes == null) {
            return [
                'status' => '0',
                'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                'order_id' => $input['order_id'],
            ];
        }

        // * Generate the session token
        $sessionTokenRes = $this->generateSessionToken($midDetails, $input, $authTokenRes["accessToken"]);
        Log::info(["payzentric-session-token-res" => $sessionTokenRes]);
        if ($sessionTokenRes == null) {
            return [
                'status' => '0',
                'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                'order_id' => $input['order_id'],
            ];
        }

        // * Now Initiate the transaction
        $txnResponse = $this->createTransaction($sessionTokenRes["accessToken"], $input, $midDetails);

        if (empty($txnResponse) && $txnResponse["data"] == null) {
            return [
                'status' => '0',
                'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                'order_id' => $input['order_id'],
            ];
        }

        if (isset($txnResponse["message"]) && $txnResponse["message"]["code"] == 100) {
            return [
                "status" => "1",
                "reason" => isset($txnResponse["message"]["description"]) && $txnResponse["message"]["description"] != "" ? $txnResponse["message"]["description"] : "Transansaction processes."
            ];
        } else {
            return [
                'status' => '0',
                'reason' => $txnResponse['message']["description"] ?? "We are facing temporary issue from the bank side. Please contact us for more detail.",
                'order_id' => $input['order_id'],
            ];
        }
    }

    // * To generate the auth token
    public function generateAuthToken(object $mid)
    {
        $response = Http::post("https://api." . $mid->login_domain . ".com/api/v1/user/token", [
            "loginDomain" => $mid->login_domain,
            "username" => $mid->username,
            "password" => $mid->password
        ])->json();
        Log::info(["payzentric-auth-token" => json_encode($response)]);
        if (!empty($response)) {
            return $response["data"];
        } else {
            return null;
        }
    }

    // * generate the session Token
    public function generateSessionToken(object $mid, array $input, string $accessToken)
    {

        $requestPayload = [
            "session" => [
                "cart" => [
                    "totalAmount" => $input['converted_amount'],
                    "currency" => $input["converted_currency"],
                    "merchantRefId" => $input["session_id"]
                ],
                "customer" => [
                    "id" => $mid->username,
                    "account" => $mid->username,
                    "password" => "",
                    "creationDate" => date('Y-m-d H:i:s'),
                    "creationIp" => $input["ip_address"],
                    "website" => self::websiteUrl,
                    "firstName" => $input["first_name"],
                    "middleName" => "",
                    "lastName" => $input["last_name"],
                    "lastNameSecond" => "",
                    "dateOfBirth" => generateRandomDob(),
                    "emailAddresses" => [
                        [
                            "emailAddress" => $input["email"],
                            "isPrimary" => true
                        ]
                    ],
                    "phoneNumbers" => [
                        [
                            "countryCode" => getCountryCode($input["country"]),
                            "areaCode" => "000",
                            "number" => $input["phone_no"],
                            "isPrimary" => true
                        ]
                    ],
                    "addresses" => [
                        [
                            "addressType" => "billing",
                            "countryCode" => getCountryCode($input["country"]),
                            "iso31661CountryChar2Code" => $input["country"],
                            "streetAddress" => $input["address"],
                            "streetAddress2" => "UNKNOWN",
                            "cityName" => $input["city"],
                            "zipPostalCode" => $input["zip"] == "N/A" || $input["zip"] == "" ? "UNKNOWN" : $input["zip"],
                            "stateIsoCode" => "UNKNOWN",
                            "isPrimary" => true
                        ]

                    ]

                ],
                "currentSessionData" => [
                    "customerIp" => $input["ip_address"],
                    "userAgent" => "UNKNOWN",
                    "httpReferer" => "https://api." . $mid->login_domain . ".com",
                    "interface" => "TAG Backoffice",
                ],
                "customerLanguage" => "English",
                "directApi" => [
                    "enabled" => true,
                    "integrationClient" => "UNKNOWN",
                    "merchantCashierUrl" => route('payzentric.return', [$input["session_id"]])
                ]
            ]
        ];

        $url = "https://capi." . strtolower($mid->login_domain) . ".com/api/generate_session";
        Log::info(["payzentric-session-payload" => json_encode($requestPayload), "session_url" => $url, "token" => "Bearer " . $accessToken]);
        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $accessToken,
        ])->post($url, $requestPayload)->json();

        if ($response != null && $response["data"] != null && $response["message"]["code"] == 100) {
            return $response["data"];
        } else {
            return null;
        }
    }

    // * Create the transaction 
    public function createTransaction(string $sessionToken, array $input, object $mid): array
    {
        $payload = [
            "transaction" => [
                "transactionType" => "deposit",
                "currency" => $input["converted_currency"],
                "methodOption" => "card",
                "amount" => $input["converted_amount"],
                "methodInfo" => [
                    "creditcardNumber" => $input["card_no"],
                    "expirationMonth" => $input["ccExpiryMonth"],
                    "expirationYear" => $input["ccExpiryYear"],
                    "cardHolderNameFirst" => $input["first_name"],
                    "cardHolderNameLast" => $input["last_name"],
                    "securityNumber" => $input["cvvNumber"]
                ]
            ]
        ];
        $response = Http::withHeaders(["Authorization" => "Bearer " . $sessionToken])->post("https://capi." . $mid->login_domain . ".com/api/create_transaction", $payload)->json();
        Log::info(["payzentric-create-txn-res" => $response]);
        return $response;
    }

    // * Payzentric return url
    public function return (Request $request, $id)
    {
        $resposne = $request->all();
        Log::info(["payzentric-return-response" => $resposne]);
    }


    public function webhook(Request $request)
    {
        $response = $request->all();
        Log::info(["payzentric-webhook" => $response]);
        if (isset($response["transaction"]) && count($response["transaction"]) > 0) {
            $transaction = DB::table('transaction_session')->select("request_data")->where("transaction_id", $response["transaction"]["merchantTransactionId"])->first();
            if ($transaction == null) {
                exit();
            }
            $input = json_decode($transaction->request_data, true);
            if (isset($response["transaction"]["pspMessage"]) && $response["transaction"]["pspMessage"] == "OK") {
                $input["status"] = "1";
                $input["reason"] = "Transaction processed successfully!";
            } else if (isset($response["transaction"]["pspMessage"]) && $response["transaction"]["pspMessage"] == "ERROR") {
                $input["status"] = "0";
                $input["reason"] = "Your Transaction could not processed!";
            } else if (isset($response["transaction"]["pspMessage"]) && $response["transaction"]["pspMessage"] == "PENDING") {
                $input["status"] = "2";
                $input["reason"] = "Your transaction is under process. please wait for sometime!";
            } else {
                $input["status"] = "0";
                $input["reason"] = "Your Transaction could not processed!";
            }

            $this->storeTransaction($input);
        }
    }

}