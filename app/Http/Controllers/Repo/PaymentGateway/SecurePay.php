<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SecurePay extends Controller
{
    use StoreTransaction;

    protected $transaction;

    // For Live URL just replace sandbox to gate

    const BASE_URL = 'https://sandbox.kapopay.com/';
    // const BASE_URL = 'https://infotrend.kapopay.com/';


    public function __construct()
    {
        $this->transaction = new Transaction;
    }

    public function checkout($input, $check_assign_mid)
    {
        
        $ShopId = $check_assign_mid->shop_id;
        $password = $check_assign_mid->password;
        $Account_id = $check_assign_mid->account_id;
        $key = $check_assign_mid->key;
        //$orderId = uniqid();
        $signature = hash("sha256", $input["session_id"].$key); 
        $postData = [
           "Credentials" => [
                 "AccountId" => $Account_id, 
                 "Signature" => $signature
              ], 
           "CustomerDetails" => [
                    "FirstName" => $input["user_last_name"], 
                    "LastName" => $input["user_first_name"], 
                    "CustomerIP" => $input["request_from_ip"], 
                    "Phone" => $input["user_phone_no"], 
                    "Email" => $input['user_email'], 
                    "Street" => $input["user_address"], 
                    "City" => $input["user_city"], 
                    "Region" => "", 
                    "Country" => $input["user_country"], 
                    "Zip" => $input["user_zip"] 
                 ], 
           "CardDetails" => [
                       "CardHolderName" => $input["user_first_name"] ." ".$input["user_last_name"], 
                       "CardNumber" => $input["user_card_no"], 
                       "CardExpireMonth" => $input["user_ccexpiry_month"], 
                       "CardExpireYear" => str_replace("20", "", $input['user_ccexpiry_year']), 
                       "CardSecurityCode" => $input["user_cvv_number"] 
                    ], 
           "ProductDescription" => "Tv Product", 
           "TotalAmount" => $input['converted_amount'] * 100, 
           "CurrencyCode" => $input["converted_currency"], 
           "TransactionId" => $input["session_id"], 
           // "CallbackURL" => "https://webhook.site/60aafdff-eeeb-46e8-ad83-f989ca7de764", 
           "CallbackURL" => route("securepay.webhook",$input["session_id"]), 
           // "ReturnUrl" => "https://webhook.site/60aafdff-eeeb-46e8-ad83-f989ca7de764", 
           "ReturnUrl" => route("securepay.return",$input["session_id"]), 
           "Custom" => "var='" .  $input["session_id"] . "'&var1=321&var3=456" 
        ]; 
        $auth= base64_encode("$ShopId:$password");
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => self::BASE_URL."process/payment/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION  => false,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "authorization: Basic $auth",
            "content-type: application/json"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);  
        curl_close($curl);
        $responseData = json_decode($response,true);
        if(isset($responseData)){
            if(isset($responseData["ConfirmationNumber"])) {
                $input['gateway_id'] = $responseData["ConfirmationNumber"] ?? "";
                $this->updateGatewayResponseData($input, $responseData);
            }
            if($responseData["Code"] == "1001" && $responseData["PaymentStatus"] == "APPROVED"){
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was processed successfully.';
            }else if($responseData["Code"] == 99){
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                    'redirect_3ds_url' => $responseData["SecurePage"],
                ];
            }
            else {
                $input['status'] = '0';
                $input['reason'] = (isset($responseData["Description"]) && !empty($responseData["Description"]) ? $responseData["Description"] : 'Your transaction could not processed.');
            }
        }else{
            $input['status'] = '0';
            $input['reason'] = 'Your transaction could not processed.';
        }
        return $input;
    }


    public function redirect(Request $request, $sessionId)
    {
        $response = $request->all();
        $transaction_session = DB::table('transaction_session')
            ->select("request_data")
            ->where('transaction_id', $sessionId)
            ->first();
        if ($transaction_session == null) {
            return abort(404);
        }

        $input = json_decode($transaction_session->request_data, true);
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
        $orderId = $input["transaction_id"];
        $signature = hash("sha256", $orderId.$check_assign_mid->key);     
        $postData["Credentials"]["AccountId"] = $check_assign_mid->account_id;
        $postData["Credentials"]["Signature"] = $signature;
        $postData["TransactionId"] = $orderId;
        $postData["ConfirmationNumber"] = $request->id;
        $auth= base64_encode("$check_assign_mid->ShopId:$check_assign_mid->password");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => self::BASE_URL."process/status/",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_FOLLOWLOCATION  => false,  
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode($postData),
          CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "authorization: Basic $auth",
            "content-type: application/json"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);  
        curl_close($curl);
        $responseData = json_decode($response,true);
        \Log::info([
            'transaction_response' => $responseData
        ]);
        if($responseData["Code"] == "1001" && $responseData["PaymentStatus"] == "APPROVED")
        {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was processed successfully.';
        }else if($responseData["Code"] == 99){
            $input['status'] = '2';
            $input['reason'] = 'Transaction is pending in acquirer system, please check after few minutes.';
        }
        else{
            $input['status'] = '0';
            $input['reason'] = (isset($responseData["Description"]) && !empty($responseData["Description"]) ? $responseData["Description"] : 'Your transaction could not processed.');
        }
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function webhook(Request $request, $sessionId)
    {
        $responseData = $request->all();
        $data = DB::table('transaction_session')
            ->select("request_data")
            ->where('transaction_id', $sessionId)->first();
        if ($data == null) {
            abort(404);
        }
        sleep(5);
        $input = json_decode($data->request_data, 1);
        if($responseData["Code"] == "1001" && $responseData["PaymentStatus"] == "APPROVED")
        {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was processed successfully.';
        }else{
            $input['status'] = '0';
            $input['reason'] = (isset($responseData["Description"]) && !empty($responseData["Description"]) ? $responseData["Description"] : 'Your transaction could not processed.');
        }
        $transaction_response = $this->storeTransaction($input);
        exit();
    }
}