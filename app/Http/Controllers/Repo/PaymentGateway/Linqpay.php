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
               "RedirectUrl" => "https://webhook.site/bd34da35-baa7-4ef9-929b-1e91fc0dddf1" 
            ] 
        ]; 
        curl_setopt_array($curl, array(
          CURLOPT_URL => self::BASE_URL.'data/encrypt',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => json_encode($arrCreateOrder),
          CURLOPT_HTTPHEADER => array(
            'api-key: '.$check_assign_mid->api_key,
            'Content-Type: application/json'
          ),
        ));

        $create_order_response = curl_exec($curl);
        curl_close($curl);
        $createOrderResponse = json_decode($create_order_response, true);
        \Log::info(["createOrderResponse" => $createOrderResponse]);
        if(isset($createOrderResponse["data"])){
            $curl = curl_init();
            $arrOrder = ["data" => $createOrderResponse["data"]];
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

                }else{
                    $curlCard = curl_init();
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
                    \Log::info(["cardArr" => $cardArr]);
                    curl_setopt_array($curlCard, array(
                      CURLOPT_URL => self::BASE_URL.'data/encrypt',
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => '',
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => 'POST',
                      CURLOPT_POSTFIELDS => json_encode($cardArr),
                        CURLOPT_HTTPHEADER => array(
                            'api-key: '.$check_assign_mid->api_key,
                            'Content-Type: application/json'
                        ),
                    ));
                    $response = curl_exec($curlCard);
                    curl_close($curlCard);
                    $responseData = json_decode($response,true);
                    \Log::info(["responseData" => $responseData]);
                    if(isset($responseData["data"])){
                        $curlPay = curl_init();
                        $arrPay = [ "data" => $responseData["data"]];
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
                        $input['status'] = '0';
                        $input['reason'] = 'Transaction declined.';
                    }
                }
            }else{
                $input["gateway_id"] = isset($input['session_id']) ? $input['session_id'] : '1';
                $this->updateGatewayResponseData($input, $response_create);
                $input['status'] = '0';
                $input['reason'] = 'Transaction declined.';
            }
        }else{
            $input["gateway_id"] = isset($input['session_id']) ? $input['session_id'] : '1';
            $this->updateGatewayResponseData($input, $create_order_response);
            $input['status'] = '0';
            $input['reason'] = 'Transaction declined.';
        }
        return $input;
    }

    public function redirect(Request $request, $session_id) {
        \Log::info([
            'chargemoney-redirect' => $request->all(),
        ]);

        $request_data = $request->all();
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        $input['gateway_id'] = isset($request_data['order_id']) ? $request_data['order_id'] : "1";
        // $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);
        if (isset($request_data['status']) && $request_data['status'] == 'success') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
        } else if (isset($request_data['status']) && $request_data['status'] == 'declined' || $request_data['status'] == 'fail') {
            $input['status'] = '0';
            $input['reason'] = (isset($request_data['message']) ? $request_data['message'] : 'Your transaction could not processed.');
        } else if (isset($request_data['status']) && $request_data['status'] == 'blocked') {
            $input['status'] = '5';
            $input['reason'] = (isset($request_data['message']) ? $request_data['message'] : 'Your transaction could not processed.');
        } else {
            $input['status'] = '2';
            $input['reason'] = 'Transaction is in pending.';
        }
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function callback(Request $request, $session_id) {
        \Log::info([
            'chargemoney-callback' => $request->all(),
        ]);
        sleep(10);
        $request_data = $request->all();
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        $input['gateway_id'] = isset($request_data['order_id']) ? $request_data['order_id'] : "1";
        $input["descriptor"] = isset($request_data["descriptor"]) ? $request_data["descriptor"] : "";
        if (isset($request_data['transaction_status']) && $request_data['transaction_status'] == 'success') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
            $this->storeTransaction($input);
        } else if (isset($request_data['transaction_status']) && $request_data['transaction_status'] == 'fail') {
            $input['status'] = '0';
            $input['reason'] = (isset($request_data['reason']) ? $request_data['reason'] : 'Your transaction could not processed.');
            $this->storeTransaction($input);
        } else if (isset($request_data['transaction_status']) && $request_data['transaction_status'] == 'pending') {
            $input['status'] = '2';
            $input['reason'] = "Transaction is in pending.";
            $this->storeTransaction($input);
        } else if (isset($request_data['transaction_status']) && $request_data['transaction_status'] == 'blocked') {
            $input['status'] = '5';
            $input['reason'] = (isset($request_data['reason']) ? $request_data['reason'] : 'Your transaction could not processed.');
            $this->storeTransaction($input);
        }
        exit();
    }

    public function statusApi($request_data, $input)
    {

        $mid = checkAssignMID($input["payment_gateway_id"]);
        $payload = [
            "api_key" => $mid->api_key,
            "order_id" => $request_data["customer_order_id"]
        ];
        // $response = Http::post(self::STATUS_API, $payload)->json();
        $response = $this->curlPostRequest(self::STATUS_API, $payload);
        if (!empty($response) && $response != null) {
            return $response;
        } else {
            return null;
        }
    }

    public function curlPostRequest($url, $data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}
