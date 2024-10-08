<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Session;
use App\Transaction;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;

class BoomBill extends Controller
{
    use StoreTransaction;
    
    const BASE_URL = 'https://gateway.boom-bill.com/rest/v1/';

    public function checkout($input, $check_assign_mid)
    {
        $card_type = "";
        if($input["card_type"] == 1){
            $card_type = "AMEX";
        }else if($input["card_type"] == 2){
            $card_type = "VISA";
        }else if($input["card_type"] == 3){
            $card_type = "MASTERCARD";
        }else if($input["card_type"] == 4){
            $card_type = "DISCOVER";
        }else if($input["card_type"] == 5){
            $card_type = "JCB";
        }else if($input["card_type"] == 6){
            $card_type = "MAESTRO";
        }else if($input["card_type"] == 7){
            $card_type = "SWITCH";
        }else if($input["card_type"] == 8){
            $card_type = "SOLO";
        }else if($input["card_type"] == 9){
            $card_type = "UNIONPAY";
        }
        $phone_no = str_replace('+', '', $input["phone_no"]);
        if(strlen($phone_no) > 12){
            $phone_no = substr($phone_no, -12);
        }elseif(strlen($phone_no) < 9){
            $country_code = "1";
            if(isset($input["country_code"]) && !empty($input["country_code"])){
                $country_code = $input["country_code"];
            }
            $phone_no = $country_code."".$phone_no;
        }
        $data = [
            "key" => $check_assign_mid->key,
            "last_name" => $input["last_name"],
            "first_name" => $input["first_name"],
            "amount" => strval($input['converted_amount']),
            'email' => $input['email'],
            "phone" => $phone_no,
            "address" => $input["address"],
            "city" => $input["city"],
            "state" => $input["state"],
            "country" => $input["country"],
            "zip_code" => $input["zip"],
            "amount" => $input["converted_amount"],
            "currency" => $input["converted_currency"],
            "pay_by" => $card_type,
            "card_number" => $input["card_no"],
            "card_name" => $input["first_name"]." ".$input["last_name"],
            "expiry_month" => $input["ccExpiryMonth"],
            "expiry_year" => $input["ccExpiryYear"],
            "cvv_code" => $input["cvvNumber"],
            "orderid" => $input["order_id"],
            "clientip" => "3.8.25.32",
            "redirect_url"=> route("boombill.redirect",$input["session_id"]),
            "webhook_url"=> route("boombill.webhook",$input["session_id"]),
        ];
        // \Log::info([
        //     'boombill-request-data' => $data,
        // ]);
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://gateway.boom-bill.com/rest/v1/payment',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>json_encode($data,JSON_UNESCAPED_SLASHES),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Access-Control-Allow-Origin:api',
            'APPKEY:'.$check_assign_mid->app_key,
            'Header-Token:'.$check_assign_mid->header_token
          ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $response_data = json_decode($response);
        // \Log::info([
        //     'boombill-response' => $response_data,
        // ]);
        if(isset($response_data)){
            $input['gateway_id'] = isset($response_data->data->paymentId) ? $response_data->data->paymentId : null;
            $this->updateGatewayResponseData($input, $response_data);
        }
        if(isset($response_data->message) && $response_data->message == "INITIATED"){
            try {
                
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                    'redirect_3ds_url' => $response_data->data->redirectUrl,
                ];
            } catch (Exception $e) {
                \Log::info([
                    'boombill-exception' => $e->getMessage()
                ]);
                return [
                    'status' => '0',
                    'reason' => $e->getMessage(),
                    'order_id' => $input['order_id']
                ];
            }
        }elseif(isset($response_data->success) && $response_data->success == false){
            if($response_data->message == "DECLINED"){
                return [
                    'status' => '0',
                    'reason' => (isset($response_data->data->gatewayResponse) ? $response_data->data->gatewayResponse : 'Your transaction could not processed.'),
                    'order_id' => $input['order_id']
                ];
            }else{
                return [
                    'status' => '0',
                    'reason' => (isset($response_data->message) ? $response_data->message : 'Your transaction could not processed.'),
                    'order_id' => $input['order_id']
                ];
            }
            
        }elseif(isset($response_data->success) && $response_data->success == true){
            if($response_data->message == "APPROVED"){
                return [
                    'status' => '1',
                    'reason' => (isset($response_data->data->gatewayResponse) ? $response_data->data->gatewayResponse : 'Your transaction has been processed successfully.'),
                    'order_id' => $input['order_id']
                ];
            }
        }else{
            return [
                'status' => '0',
                'reason' => 'Your transaction could not processed.',
                'order_id' => $input['order_id']
            ];
        }
    }

    public function redirect(Request $request,$session_id){
        $request_data = $request->all();
        // \Log::info([
        //     'boombill_redirect_data' => $request_data
        // ]);
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($input_json == null) {
            return abort(404);
        }        
        $input = json_decode($input_json['request_data'], true);
        $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);
        if (isset($request_data['status']) && $request_data['status'] == 'APPROVED') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
        }else if (isset($request_data['status']) && ($request_data['status'] == 'DECLINED' || $request_data['status'] == 'ERROR' )) {
            $input['status'] = '0';
            $input['reason'] = (isset($request_data['message']) ? $request_data['message'] : 'Your transaction could not processed.');
        }else {
            $input['status'] = '2';
            $input['reason'] = 'Transaction is in pending.';
        }
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function webhook(Request $request,$session_id){
        sleep(5);
        $request_data = $request->all();
        // \Log::info([
        //     'boombill_webhook_data' => $request_data,
        //     'id' => $session_id
        // ]);
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($input_json == null) {
            return abort(404);
        }        
        $input = json_decode($input_json['request_data'], true);
        if (isset($request_data['transaction_status']) && $request_data['transaction_status'] == 'APPROVED') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
            $transaction_response = $this->storeTransaction($input);
        }else if (isset($request_data['transaction_status']) && ($request_data['transaction_status'] == 'DECLINED' || $request_data['transaction_status'] == 'ERROR' )) {
            $input['status'] = '0';
            $input['reason'] = (isset($request_data['reason']) ? $request_data['reason'] : 'Your transaction could not processed.');
            $transaction_response = $this->storeTransaction($input);
        }
        exit();
    }
}
