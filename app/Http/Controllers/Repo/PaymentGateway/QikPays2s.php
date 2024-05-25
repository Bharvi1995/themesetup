<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\TransactionSession;
use Illuminate\Support\Facades\Crypt;

class QikPays2s extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://secure.qikpay.co.in/pgui/jsp/merchantPaymentInit'; // live
    //const BASE_URL = 'https://sandbox.qikpay.co.in/pgui/jsp/merchantPaymentInit'; // test
    
    public function checkout($input, $check_assign_mid) {
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $input["session_id"])
            ->first();

        if ($data == null) {
            return abort(404);
        }
        $inputData = json_decode($data->request_data, 1);
        $cardDetails = $input["card_no"]."_".$input["ccExpiryMonth"]."20".$input["ccExpiryYear"]."_".$input["cvvNumber"];
        $inputData["reqest_data"] = \Crypt::encryptString($cardDetails);
        \DB::table('transaction_session')->where('transaction_id', $input["session_id"])->update(["request_data"=>json_encode($inputData)]);
        return [
            'status' => '7',
            'reason' => '3DS link generated successfully, please redirect.',
            'redirect_3ds_url' => route('qikpays2s.form', $input['session_id'])
        ];
    }

    public function requestString($array, $salt_key) {
        sort($array);
        $merchant_data_string = implode('~', $array);
        $format_Data_string = $merchant_data_string . $salt_key;
        $hashData_uf = hash('sha256', $format_Data_string);
        $hashData = strtoupper($hashData_uf);
        $hashValue='~HASH='.$hashData;
        $finalString = $merchant_data_string.$hashValue;
        return $finalString;
    }

    public function form($session_id){
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first();
        if ($data == null) {
            return abort(404);
        }
        $input = json_decode($data->request_data, 1);
        $userData = \App\User::find($input["user_id"]);
        return view("gateway.qikpay.qikpay",compact('input','userData'));
    }

    public function formSubmit(Request $request, $session_id){
        if($request->payment_type == "CC" || $request->payment_type == "DC" || $request->payment_type == "UP"){
            $data = \DB::table('transaction_session')
                ->where('transaction_id', $session_id)
                ->first();

            if ($data == null) {
                return abort(404);
            }
            $input = json_decode($data->request_data, 1);
            $userData = \App\User::find($input["user_id"]);
            return view('gateway.qikpay.details',compact('request','input','userData'));
        }else{
            return $this->formSendData($request, $session_id);
        }
    }

    public function formSendData(Request $request,$session_id){
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first();
        if ($data == null) {
            return abort(404);
        }
        $input = json_decode($data->request_data, 1);
        
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
        $pay_id = $check_assign_mid->PAY_ID;

        $data = [
            'PAY_ID' => $pay_id,
            'ORDER_ID' => $input['order_id'],
            'PAYMENT_TYPE' => $request->payment_type,
            'MOP_TYPE' => $request->mop_id,
            'AMOUNT' => ceil($input["converted_amount"])*100,
            'CURRENCY_CODE' => '356',
            'CUST_EMAIL' => $input["email"],
            'CUST_NAME' =>  $input["first_name"],
            'CUST_PHONE' => $input['phone_no'],
            'PRODUCT_DESC' => $input["session_id"],
            'RETURN_URL' => Route("qikpays2s-callback",$input["session_id"])
        ];
        if($request->payment_type == "CC" || $request->payment_type == "DC"){
            //$data['PAYMENT_TYPE'] = 'CARD';
            $data["CARD_NUMBER"] = str_replace(" ", "", $request->card_no);
            $ccExpiryMonth = substr($request->ccExpiryMonthYear, 0, 2);
            $ccExpiryYear = substr($request->ccExpiryMonthYear, -2);
            $data["CARD_EXP_DT"] = $ccExpiryMonth."".'20'.$ccExpiryYear;
            $data["CVV"] = '936';
            $data['CARD_HOLDER_NAME'] = $input["first_name"]." ".$input["last_name"];
        }
        else if($request->payment_type == "UP"){
            $data["PAYER_ADDRESS"] = $request->txtUPI;
        }
        foreach ($data as $key => $value) {
            $requestParamsJoined[] = "$key=$value";
        }
        $CryptoKey= "E517DEF0C34A3EDAA567263A27EE65CC";
        $iv = substr($CryptoKey, 0, 16); 
        $method = "AES-256-CBC";
        $salt = $check_assign_mid->secret_key;
        $requestString= $this->requestString($requestParamsJoined, $salt);
        $ciphertext = openssl_encrypt($requestString, $method, $CryptoKey, OPENSSL_RAW_DATA, $iv);
        $encdata = base64_encode($ciphertext);
        $url = self::BASE_URL;
        return view("gateway.qikpay.form",compact('encdata','pay_id','url'));
    }

    public function callback($id, Request $request) {
        $response = $request->all();
        \Log::info([
            'qikpay-callback' => $response,
            'id' => $id
        ]);
        if (! empty($id)) {
            $transaction_session = DB::table('transaction_session')
                ->where('transaction_id', $id)
                ->first();
            if ($transaction_session == null) {
                $error = 'Transaction not found.';
            }
            $input = json_decode($transaction_session->request_data, 1);
            if(isset($response["RESPONSE_CODE"]) && $response["RESPONSE_CODE"] == "000"){
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
            }else{
                $input['status'] = '0';
                $input['reason'] = (isset($response['PG_TXN_MESSAGE']) ? $response['PG_TXN_MESSAGE'] : 'Your transaction could not processed.');
            }
            unset($input["reqest_data"]);
            // $arrResponse = explode("&",$response);
            // $arrData = [];
            // foreach ($arrResponse as $key => $value) {
            //     $arrValue = explode("=",$value); 
            //     $arrData[$arrValue["0"]] = $arrValue["1"];
            // }
            // Update callback response
            $input['gateway_id'] = $response["HASH"] ?? null;
            $this->updateGatewayResponseData($input, $response);
            // store transaction
            //echo "<pre>";print_r($input);exit();
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        }
    }
}

