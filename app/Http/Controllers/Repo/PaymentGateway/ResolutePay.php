<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;


class ResolutePay extends Controller
{
    use StoreTransaction;

    const BASE_URL = "https://gateway.resolutepays.com/paymentgateway/payments/performXmlTransaction";

    public function checkout($input, $mid)
    {
        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');
        $payload = [
            "terminalid" => $mid->terminal_id,
            "password" => $mid->password,
            "action" => "1",
            "card" => $input["card_no"],
            "cvv2" => $input["cvvNumber"],
            "expyear" => $input["ccExpiryYear"],
            "expmonth" => $input["ccExpiryMonth"],
            "member" => $input["first_name"] . " " . $input["last_name"],
            "currencycode" => $input["converted_currency"],
            "address" => $input["address"],
            "city" => $input["city"],
            "statecode" => $input["zip"],
            "countrycode" => $input["country"],
            "email" => $input["email"],
            "amount" => $input["converted_amount"],
            "trackid" => $input["session_id"],
            "customerip" => $input["ip_address"],
            "phonenumber" => $input["phone_no"],
            "terminalreceipturl" => route("resolutepay.callback", [$input["session_id"]])
            // "terminalreceipturl" => "https://webhook.site/b1c92bd7-09ec-4fa4-8746-1cf92660f13e"

        ];

        // Create a SimpleXMLElement object
        $xml = new SimpleXMLElement('<request />');

        // Call the function to convert the array to XML
        $this->arrayToXml($payload, $xml);

        // Convert SimpleXMLElement object to formatted XML string
        $xmlString = $xml->asXML();
        $this->storeMidPayload($input["session_id"], json_encode($xmlString));

        $apiResponse = $this->hitCurlRequest($xmlString);

        // Log::info(["ResolutePay-Payload" => $xmlString, "ResolutePay-response" => $apiResponse]);

        // * Convert xml response to php array
        $response = $this->xmlToArray($apiResponse);
        $input["gateway_id"] = $response["payId"] ?? "1";
        $this->updateGatewayResponseData($input, $response);
        if (!isset($response) && empty($response)) {
            return [
                'status' => '0',
                'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                'order_id' => $input['order_id'],
            ];
        } else if (isset($response["result"]) && $response["result"] == "Unsuccessful") {
            return [
                "status" => "0",
                "reason" => $response["udf5"] ?? "Transaction could not processed."
            ];
        } else if (isset($response["targetUrl"])) {
            return [
                'status' => '7',
                'reason' => '3DS link generated successful, please redirect.',
                'redirect_3ds_url' => $response["targetUrl"] . $response["payId"]
            ];
        } else {
            return [
                "status" => "0",
                "reason" => "Transaction could not processed."
            ];
        }

    }

    // * Callback
    public function callback(Request $request, $id)
    {
        // $response = $request->all();
        $transaction = DB::table("transaction_session")->select("id", "request_data", "payment_gateway_id")->where("transaction_id", $id)->first();
        if ($transaction == null || empty($transaction)) {
            abort(404, "Url is not correct");
        }

        $input = json_decode($transaction->request_data, true);

        // * Hit the status API
        $mid = checkAssignMID($transaction->payment_gateway_id);
        $statusRes = $this->statusApi($mid, $id);
        if (isset($statusRes["result"]) && $statusRes["result"] == "Unsuccessful") {
            $input["status"] = "0";
            $input["reason"] = isset($statusRes["udf5"]) && $statusRes["udf5"] != "" ? $statusRes["udf5"] : $this->getErrorMessage($statusRes["responsecode"]);
        } else if (isset($statusRes["responsecode"]) && $statusRes["responsecode"] == "000") {
            $input["status"] = "1";
            $input["reason"] = "Transaction processed successfully.";
        } else if (isset($statusRes["responsecode"]) && $statusRes["responsecode"] == "002") {
            $input["status"] = "2";
            $input["reason"] = "Your transaction is under process . Please wait for sometime!";
        } else {
            $input["status"] = "0";
            $input["reason"] = "Transaction could not processed.";
        }
        $this->updateGatewayResponseData($input, $statusRes);
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);

    }

    // Hit curl request
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

    // Function to convert array to XML
    function arrayToXml($data, &$xml)
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

    public function statusApi($mid, $id)
    {

        $payload = [
            "terminalid" => $mid->terminal_id,
            "password" => $mid->password,
            "action" => "15",
            "trackid" => $id,
        ];

        // Create a SimpleXMLElement object
        $xml = new SimpleXMLElement('<request />');

        // Call the function to convert the array to XML
        $this->arrayToXml($payload, $xml);

        // Convert SimpleXMLElement object to formatted XML string
        $xmlString = $xml->asXML();

        $apiResponse = $this->hitCurlRequest($xmlString);

        // * Convert xml response to php array
        $response = $this->xmlToArray($apiResponse);
        return $response;
    }

    // * Error messages
    public function getErrorMessage($code)
    {
        $data = config("resolutePay.errors");

        if (array_key_exists($code, $data)) {
            return $data[$code];
        } else {
            return "Transaction could not processed.";
        }
    }

    public function testStatus($id)
    {
        // $mid = checkAssignMID("28");
        $payload = [
            "terminalid" => "PAME0008",
            "password" => "aG!0xxSoxl8w",
            "action" => "15",
            "trackid" => $id,

        ];

        // Create a SimpleXMLElement object
        $xml = new SimpleXMLElement('<request />');

        // Call the function to convert the array to XML
        $this->arrayToXml($payload, $xml);

        // Convert SimpleXMLElement object to formatted XML string
        $xmlString = $xml->asXML();
        $apiResponse = $this->hitCurlRequest($xmlString);



        // * Convert xml response to php array
        $response = $this->xmlToArray($apiResponse);
        return $response;
    }
}