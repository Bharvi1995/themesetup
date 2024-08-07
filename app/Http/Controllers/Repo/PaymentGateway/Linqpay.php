<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\StoreTransaction;
use App\TransactionSession;
use App\Transaction;
use Illuminate\Support\Facades\Http;
use phpseclib3\Crypt\RSA;
use phpseclib3\Math\BigInteger;
class Linqpay extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://payment-api-service.uselinq.com/payment/'; // for test
    // const BASE_URL = 'https://payment-api-service.uselinq.com/payment/'; // for live
    const STATUS_API = 'https://dashboard.charge.money/api/get-transaction-details';

    // ================================================
    /* method : __construct
    * @param  :
    * @Description : Create a new controller instance.
    */ // ==============================================
    public function __construct()
    {
        $this->transaction = new Transaction;
        $this->transactionSession = new TransactionSession;
    }

    public function checkout($input, $check_assign_mid)
    {
        $curl = curl_init();
        $arrCreateOrder = [
           "customer" => [
                 "firstname" => $input['user_first_name'], 
                 "lastname" => $input['user_last_name'], 
                 "mobile" => $input['user_phone_no'], 
                 "country" => $input['user_country'], 
                 "email" => $input['user_email'] 
              ], 
           "order" => [
                    "amount" => $input['converted_amount'], 
                    "reference" => $input['session_id'], 
                    "description" => "Pay", 
                    "currency" => $input['converted_currency'] 
                 ], 
           "payment" => [
               // "RedirectUrl" => "https://webhook.site/bd34da35-baa7-4ef9-929b-1e91fc0dddf1" 
               "RedirectUrl" => route('linqpay.return',$input["session_id"]) 
            ] 
        ]; 
        $data = json_encode($arrCreateOrder);
        $encrypted_data = encryptData($data, $check_assign_mid->public_key);
        $curl = curl_init();
        // $arrOrder = ["data" => $createOrderResponse["data"]];
        $arrOrder = ["data" => $encrypted_data];
        curl_setopt_array($curl, array(
            CURLOPT_URL => self::BASE_URL.'order/create',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($arrOrder),
            CURLOPT_HTTPHEADER => array(
                'api-key: '.$check_assign_mid->api_key,
                'Content-Type: application/json'
            ),
        ));
        $response_create = curl_exec($curl);
        $responseData_create = json_decode($response_create,true);
        \Log::info(["responseData_create" => $responseData_create]);
        curl_close($curl);
        if(isset($responseData_create["data"]["order"]["statusId"]) && $responseData_create["data"]["order"]["statusId"] == 1){
            $input["gateway_id"] = isset($responseData_create["data"]["order"]["processorReference"]) ? $responseData_create["data"]["order"]["processorReference"] : '1';
            $this->updateGatewayResponseData($input, $response_create);
            if($check_assign_mid->type == 2){
                $cardArr = [
                    "reference" => $input['session_id'], 
                    "paymentoption" => "C",
                    "country" => $input['user_country'], 
                    "card" => [
                        "cardnumber" => $input["user_card_no"], 
                        "expirymonth" => $input["user_ccexpiry_month"], 
                        "expiryyear" => substr($input["user_ccexpiry_year"], -2), 
                        "cvv" => $input["user_cvv_number"],
                        "authOption" => "NOAUTH"
                    ] 
                ];
            }else{
                
                $cardArr = [
                   "reference" => $input['session_id'], 
                   "paymentoption" => "C", 
                   "card" => [
                        "cardnumber" => $input["user_card_no"], 
                        "expirymonth" => $input["user_ccexpiry_month"], 
                        "expiryyear" => substr($input["user_ccexpiry_year"], -2), 
                        "cvv" => $input["user_cvv_number"] 
                    ] 
                ];
            }
            $curlCard = curl_init();
            $dataCard = json_encode($cardArr);
            $encrypted_datacard = encryptData($dataCard, $check_assign_mid->public_key);
            \Log::info(["cardArr" => $cardArr]);
            $curlPay = curl_init();
            $arrPay = [ "data" => $encrypted_datacard];
            curl_setopt_array($curlPay, array(
                CURLOPT_URL => self::BASE_URL.'order/pay',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($arrPay),
                CURLOPT_HTTPHEADER => array(
                    'api-key: '.$check_assign_mid->api_key,
                    'Content-Type: application/json'
                ),
            ));
            $responsePay = curl_exec($curlPay);
            $responsePayData = json_decode($responsePay,true);
            \Log::info(["responsePayData" => $responsePayData]);
            curl_close($curlPay);                        
            if(isset($responsePayData) && isset($responsePayData["statusCode"]) && $responsePayData["statusCode"] == "02"){
                $input['status'] = '7';
                $input['reason'] = "Please redirect to the specified 'payment_link' to complete the transaction processing.";
                $input["payment_link"] = $responsePayData["data"]["paymentDetail"]["redirectUrl"];
            }elseif(isset($responsePayData) && isset($responsePayData["statusCode"]) && $responsePayData["statusCode"] == "00"){
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';                        
            }elseif(isset($responsePayData) && isset($responsePayData["statusCode"]) && $responsePayData["statusCode"] == "03"){
                $input['status'] = '2';
                $input['reason'] = 'Transaction is in pending.';                        
            }else{
                $input['status'] = '0';
                $input['reason'] = isset($responsePayData["message"]) ? $responsePayData["message"] : "Transaction declined.";
            }
        }else{
            $input["gateway_id"] = isset($input['session_id']) ? $input['session_id'] : '1';
            $this->updateGatewayResponseData($input, $response_create);
            $input['status'] = '0';
            $input['reason'] = 'Transaction declined.';
        }
        return $input;
    }

    public function base64url_decode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public function getXmlComponent($xmlstring, $field) {
        try {
            $xml = new SimpleXMLElement($xmlstring);
            $namespaces = $xml->getNamespaces(true);
            $xml->registerXPathNamespace('ns', $namespaces['']);
            $elements = $xml->xpath("//ns:$field");
            if ($elements) {
                return (string)$elements[0];
            } else {
                echo "Element $field not found.\n";
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
        return "";
    }

    public function encrypt($data, $public_xml) {
        try {
            if (!$data) {
                throw new Exception("Data sent for encryption is empty");
            }
            // Decode the Base64 string
            $decoded_bytes = base64_decode($public_xml);
            $decoded_string = utf8_encode($decoded_bytes);
            $public_xml_key = explode('!', $decoded_string)[1];

            $modulus = $this->getXmlComponent($public_xml_key, "Modulus");
            $exponent = $this->getXmlComponent($public_xml_key, "Exponent");

            $modulus_bytes = base64_decode($modulus);
            $exponent_bytes = base64_decode($exponent);

            // Convert modulus and exponent to hexadecimal
            $modulus_hex = bin2hex($modulus_bytes);
            $exponent_hex = bin2hex($exponent_bytes);

            $rsa = RSA::load([
                'n' => new BigInteger($modulus_hex, 16),
                'e' => new BigInteger($exponent_hex, 16)
            ]);

            // Encrypt data
            $rsa = $rsa->withPadding(RSA::ENCRYPTION_PKCS1);
            $encrypted = $rsa->encrypt($data);

            // Convert to base 64 string
            $encrypted_base64 = base64_encode($encrypted);
            echo $encrypted_base64 . "\n";

            return $encrypted;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            return "";
        }
    }

    public function return(Request $request, $session_id) {
        \Log::info([
            'Linqpay-redirect' => $request->all(),
        ]);

        $request_data = $request->all();
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);
        $curlPay = curl_init();
        $arrPay = [ "reference" => $session_id];
        $encrypted_data = encryptData(json_encode($arrPay), $check_assign_mid->public_key);
        $finalData = ["data" => $encrypted_data];
        curl_setopt_array($curlPay, array(
            CURLOPT_URL => self::BASE_URL.'order/status',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($finalData),
            CURLOPT_HTTPHEADER => array(
                'api-key: '.$check_assign_mid->api_key,
                'Content-Type: application/json'
            ),
        ));
        $responsePay = curl_exec($curlPay);
        $responsePayData = json_decode($responsePay,true);
        if(isset($responsePayData) && isset($responsePayData["statusCode"]) && $responsePayData["statusCode"] == "00"){
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
        }elseif(isset($responsePayData) && isset($responsePayData["statusCode"]) && ($responsePayData["statusCode"] == "02" || $responsePayData["statusCode"] == "03")) {
            $input['status'] = '2';
            $input['reason'] = 'Transaction is in pending.';
        }else{
            $input['status'] = '0';
            $input['reason'] = (isset($responsePayData['message']) ? $responsePayData['message'] : 'Your transaction could not processed.');
        }       
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

}
