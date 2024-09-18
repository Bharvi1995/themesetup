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
class Greenerpay extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://greenerpayments.net/api/'; // for test
    // const BASE_URL = 'https://greenerpayments.net/api/'; // for live

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
        curl_setopt_array($curl, array(
            CURLOPT_URL => self::BASE_URL.'token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "username" : "'.$check_assign_mid->username.'",
                "password" : "'. $check_assign_mid->password .'"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
         \Log::info(["response" => $response]);
        $responseData = json_decode($response,true);
        if(isset($responseData) && isset($responseData["token"]) && !empty($responseData["token"])){
            $curlPay = curl_init();
            $arr = [
               "firstname" => $input['user_first_name'], 
               "lastname" => $input['user_last_name'], 
               "email" => $input['user_email'], 
               "phone" => $input['user_phone_no'], 
               "country" => $input['user_country'], 
               "city" => $input['user_city'], 
               "state" => $input['user_state'], 
               "zip_code" => $input['user_zip'], 
               "address" => $input['user_address'], 
               "amount" => $input['converted_amount'], 
               "currency" => $input['converted_currency'] , 
               "cardName" => "Test Card", 
               "cardNumber" => $input["user_card_no"], 
               "expMonth" => $input["user_ccexpiry_month"], 
               "expYear" => substr($input["user_ccexpiry_year"], -2), 
               "cardCVV" => $input["user_cvv_number"], 
               "reference" => $input['session_id'], 
               "ip_address" => $input["request_from_ip"], 
               // "ip_address" => "122.170.155.188", 
               "callback_url" => route('greenerpay.return',$input["session_id"]) 
            ]; 
            curl_setopt_array($curlPay, array(
              CURLOPT_URL => self::BASE_URL.'charge',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => json_encode($arr),
              CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$responseData["token"],
                'Content-Type: application/json',
              ),
            ));
            $responsePay = curl_exec($curlPay);
            \Log::info(["response" => $responsePay]);
            curl_close($curlPay);
            $responsePayData = json_decode($responsePay,true);
            $input["gateway_id"] = isset($responsePayData["data"]["orderid"]) ? $responsePayData["data"]["orderid"] : '1';
            $this->updateGatewayResponseData($input, $responsePayData);
            if(isset($responsePayData) && isset($responsePayData["data"]["link"])){
                $input['status'] = '7';
                $input['reason'] = "Please redirect to the specified 'payment_link' to complete the transaction processing.";
                $input["payment_link"] = $responsePayData["data"]["link"];
            }else{
                $input['status'] = '0';
                $input['reason'] = isset($responsePayData["data"]) ? $responsePayData["data"] : "Transaction declined.";
            }
        }else{
            $input["gateway_id"] = isset($input['session_id']) ? $input['session_id'] : '1';
            $this->updateGatewayResponseData($input, $responseData);
            $input['status'] = '0';
            $input['reason'] = 'Transaction declined.';
        }
        return $input;
    }


    public function return(Request $request, $session_id) {
        \Log::info([
            'Greenerpay-redirect' => $request->all(),
        ]);

        $request_data = $request->all();
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        if(isset($request_data) && isset($request_data["status"]) && $request_data["status"] == "approved"){
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
        }else{
            $input['status'] = '0';
            $input['reason'] = (isset($request_data['message']) ? $request_data['message'] : 'Your transaction could not processed.');
        }       
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

}
