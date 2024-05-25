<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;

class DpoPay extends Controller
{

    use StoreTransaction;

    const BASE_URL = "https://secure.3gdirectpay.com/API/v6/";
    const PAY_URL = "https://secure.3gdirectpay.com/payv2.php?ID=";

    public function checkout($input, $mid)
    {
        try {
            $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');
            $tokenPayload = [
                "CompanyToken" => $mid->company_token,
                "Request" => "createToken",
                "Transaction" => [
                    "PaymentAmount" => $input["converted_amount"],
                    "PaymentCurrency" => $input["converted_currency"],
                    "CompanyRef" => $input["session_id"],
                    "RedirectURL" => route("dpo.redirecturl", [$input["session_id"]]),
                    "BackURL" => route('dpo.backurl', [$input["session_id"]]),
                    "CompanyRefUnique" => 1,
                    "customerEmail" => $input["email"],
                    "customerFirstName" => $input["first_name"],
                    "customerLastName" => $input["last_name"],
                    "customerAddress" => $input["address"],
                    "customerCity" => $input["city"],
                    "customerCountry" => $input["country"],
                    "customerPhone" => $input["phone_no"],
                    "customerZip" => $input["zip"],
                    "EmailTransaction" => "0",
                    "DefaultPayment" => "CC",
                ],
                "Services" => [
                    "Service" => [
                        "ServiceType" => 84120,
                        "ServiceDescription" => "Card transaction " . $input["order_id"],
                        "ServiceDate" => now()->format("Y/m/d h:m")
                    ]
                ]
            ];

            $tokenXmlString = $this->getXml($tokenPayload);
            // * Hit the curl request
            $createTokenRes = $this->hitCurlRequest($tokenXmlString);

            Log::info(["payload" => $tokenXmlString, "createTokenResponse" => $createTokenRes]);
            $createTokenResArray = $this->xmlToArray($createTokenRes);

            // * Update response in table
            $input["gateway_id"] = $createTokenResArray["TransToken"] ?? "1";
            $this->updateGatewayResponseData($input, $createTokenResArray);

            if (empty($createTokenResArray)) {
                return [
                    'status' => '0',
                    'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                    'order_id' => $input['order_id'],
                ];
            }

            if (isset($createTokenResArray["TransToken"])) {
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successful, please redirect.',
                    'redirect_3ds_url' => self::PAY_URL . $createTokenResArray["TransToken"]
                ];
            } else {
                return [
                    "status" => "0",
                    "reason" => "Transaction could not processed."
                ];
            }



            // $year = str_replace("20", "", $input["ccExpiryYear"]);

            // * Add Card payload
            // $cardPayload = [
            //     "CompanyToken" => $mid->company_token,
            //     "Request" => "chargeTokenCreditCard",
            //     "TransactionToken" => $createTokenResArray["TransToken"],
            //     "CreditCardNumber" => $input["card_no"],
            //     "CreditCardExpiry" => $input["ccExpiryMonth"] . $year,
            //     "CreditCardCVV" => $input["cvvNumber"],
            //     "CardHolderName" => $input["first_name"] . " " . $input["last_name"],
            //     "ChargeType" => "",
            //     "ThreeD" => [
            //         "Enrolled" => "Y",
            //         "Paresstatus" => "Y",
            //         "Eci" => "05",
            //         "Xid" => "DYYVcrwnujRMnHDy1wlP1Ggz8w0",
            //         "Cavv" => "mHyn+7YFi1EUAREAAAAvNUe6Hv8=",
            //         "Signature" => "_",
            //         "Veres" => "AUTHENTICATION_SUCCESSFUL",
            //         "Pares" => "eAHNV1mzokgW/isVPY9GFSCL0EEZkeyg7"
            //     ]
            // ];

            // $cardXml = $this->getXml($cardPayload);
            // * Hit the curl request
            // $cardApiRes = $this->hitCurlRequest($cardXml);
            // $cardResArray = $this->xmlToArray($cardApiRes);

            // $this->updateGatewayResponseData($input, $cardResArray);

            // // * Store mid payload in table
            // $cardPayload["CreditCardNumber"] = cardMasking($cardPayload["CreditCardNumber"]);
            // $cardPayload["CreditCardCVV"] = "XXX";
            // $data["cardPayload"] = $cardPayload;
            // $data["tokenPayload"] = $tokenPayload;
            // $this->storeMidPayload($input["session_id"], json_encode($data));

            // Log::info(["card_token_res" => $cardApiRes, "payload" => $cardXml, "arrayRes" => $cardResArray]);

            // if (isset($cardResArray["Result"]) && $cardResArray["Result"] == "000") {
            //     return [
            //         "status" => "1",
            //         "reason" => "Transaction processed successfully!"
            //     ];
            // } else if (isset($cardResArray["Result"]) && $cardResArray["Result"] == "200") {
            //     return [
            //         "status" => "2",
            //         "reason" => "Transaction initiated successfully!"
            //     ];
            // } else {
            //     return [
            //         "status" => "0",
            //         "reason" => $cardResArray["ResultExplanation"] ?? "Transaction could not processed!"
            //     ];
            // }

        } catch (\Exception $err) {
            Log::info(["DpoPay_error" => $err->getMessage()]);
            return [
                "status" => "0",
                "reason" => "We are facing temporary issue from the bank side. Please contact us for more detail."
            ];
        }

    }

    public function redirectHandler(Request $request, $id)
    {
        $response = $request->all();
        Log::info(["dpo-redirect-callback" => $response]);
        return $this->redirectionHandler($response, $id);
    }

    public function backHandler(Request $request, $id)
    {
        $response = $request->all();
        Log::info(["dpo-back-callback" => $response]);
        return $this->redirectionHandler($response, $id);
    }



    // * Curl APi request
    public function hitCurlRequest($xmlPayload)
    {
        $ch = curl_init();

        if (!$ch) {
            die("Couldn't initialize a cURL handle");
        }
        curl_setopt($ch, CURLOPT_URL, self::BASE_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlPayload);

        $result = curl_exec($ch);

        return $result;
    }

    // To Get the XML
    function getXml($payload)
    {
        // Create a SimpleXMLElement object
        $tokenXml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><API3G />');

        // Call the function to convert the array to XML
        $this->arrayToXml($payload, $tokenXml);

        // Convert SimpleXMLElement object to formatted XML string
        $tokenXmlString = $tokenXml->asXML();

        return $tokenXmlString;
    }

    // Function to convert array to XML
    function arrayToXml($data, $xml)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // If the value is an array, create a child element and call the function recursively
                $subnode = $xml->addChild($key);
                $this->arrayToXml($value, $subnode);
            } else {
                // If the value is not an array, add it as a child element
                $xml->addChild($key, htmlspecialchars($value));
            }
        }
    }

    // * XML to php Array
    function xmlToArray($response)
    {
        // Convert XML to SimpleXMLElement
        $xml = simplexml_load_string($response);

        // Convert SimpleXMLElement to JSON and then to PHP array
        $json = json_encode($xml);
        $responseArray = json_decode($json, true);

        return $responseArray;
    }

    // * Redirection Handler
    public function redirectionHandler(array $response, string $id)
    {

        $transaction = DB::table("transaction_session")->select("id", "request_data", "payment_gateway_id")->where("transaction_id", $id)->first();
        if ($transaction == null || empty($response)) {
            abort(404, "Url is not correct");
        }

        $input = json_decode($transaction->request_data, true);
        $mid = checkAssignMID($transaction->payment_gateway_id);
        // * Status API Payload 
        $payload = [
            "CompanyToken" => $mid->company_token,
            "Request" => "verifyToken",
            "TransactionToken" => $response["TransID"]
        ];

        $tokenVerifyToken = $this->getXml($payload);
        // * Hit the curl request
        $createTokenRes = $this->hitCurlRequest($tokenVerifyToken);
        Log::info(["verify-token-res" => $createTokenRes]);
        $arrRes = $this->xmlToArray($createTokenRes);

        if (isset($arrRes["Result"]) && $arrRes["Result"] === "000") {
            $input["status"] = "1";
            $input["reason"] = "Transaction processed successfully!";
        } else if (isset($arrRes["Result"]) && $arrRes["Result"] === "003") {
            $input["status"] = "2";
            $input["reason"] = "Transaction pending for approval please wait for sometime!";
        } else {
            $input["status"] = "0";
            $input["reason"] = $arrRes["ResultExplanation"] ?? "Transaction could not processed!";
        }

        $this->updateGatewayResponseData($input, $arrRes);
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }
}