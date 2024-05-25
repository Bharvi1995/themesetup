<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\User;
use App\Transaction;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use App\TransactionSession;
use Illuminate\Support\Facades\Http;
use Log;

class PrismPay extends Controller
{
    use StoreTransaction;

    // const BASE_URL = 'https://test.mycardstorage.com/3ds/api/3ds/v1/authenticate';
    const BASE_URL = 'https://prod.mycardstorage.com/3ds/api/3ds/v1/authenticate';
    // const BASE_URL_PAYMENT = "https://test.mycardstorage.com/api/api.asmx";
    const BASE_URL_PAYMENT = "https://prod.mycardstorage.com/api/api.asmx";

    public function checkout($input, $check_assign_mid)
    {
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $input["session_id"])
            ->first();
        if ($data == null) {
            return abort(404);
        }
        $inputData = json_decode($data->request_data, 1);
        $inputData['request_countrycode'] = \Config::get('countrycode.'.$input["user_country"]);
        $inputData["reqest_datacardNo"] = $input["user_card_no"];
        $inputData["reqest_datacardcvv"] = $input["user_cvv_number"];
        $country_code = 1;
        $inputData["reqest_datacountrycode"] = $country_code;
        if($check_assign_mid->is_type == "2"){
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => self::BASE_URL_PAYMENT,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:myc="https://MyCardStorage.com/">
               <soapenv:Header>
                  <myc:AuthHeader>
                     <!--REQUIRED: -->
                    <myc:ApiKey>'.$check_assign_mid->api_key.'</myc:ApiKey>
                  </myc:AuthHeader>
               </soapenv:Header>
               <soapenv:Body>
                  <myc:CreditSale_Soap>
                     <myc:creditCardSale>
                        <myc:ServiceSecurity>
                          <!-- REQUIRED: -->
                           <myc:MCSAccountID>'.$check_assign_mid->account_id.'</myc:MCSAccountID>
                        </myc:ServiceSecurity>
                        <myc:TokenData>
                            <!--REQUIRED:-->
                           <myc:CardHolderName>'.$input["user_first_name"]." ".$input["user_last_name"].'</myc:CardHolderName>
                           <!-- 4024007197692931 -->
                           <myc:CardNumber>'.$input["user_card_no"].'</myc:CardNumber>
                           <myc:ExpirationMonth>'.$input["user_ccexpiry_month"].'</myc:ExpirationMonth>
                           <myc:ExpirationYear>'.substr($input["user_ccexpiry_year"],-2).'</myc:ExpirationYear>
                           <myc:CVV>'.$input["user_cvv_number"].'</myc:CVV>
                        <!-- OPTIONAL -->
                        <!--
                           <myc:StreetAddress></myc:StreetAddress>
                           <myc:City></myc:City>
                           <myc:State></myc:State>
                           <myc:ZipCode></myc:ZipCode>
                           <myc:Country></myc:Country>
                           <myc:EmailAddress></myc:EmailAddress>
                           <myc:Phone></myc:Phone>
                           -->
                        </myc:TokenData>
                        <!--REQUIRED:-->
                        <myc:TransactionData>
                           <myc:Amount>'.$input["converted_amount"].'</myc:Amount>
                           <myc:CurrencyCode>'.$input["converted_currency"].'</myc:CurrencyCode>
                           <!--Optional - Use these for reporting & tracking :-->
                        </myc:TransactionData>
                     </myc:creditCardSale>
                  </myc:CreditSale_Soap>
               </soapenv:Body>
            </soapenv:Envelope>',
              CURLOPT_HTTPHEADER => array(
                'Accept: */*',
                // 'User-Agent: PostmanRuntime/7.36.0',
                'Accept-Encoding: gzip, deflate, br',
                'Connection: keep-alive',
                'Content-Type: text/xml'
              ),
            ));

            $response = curl_exec($curl);
            \Log::info(["2d response" => $response]);
            curl_close($curl);
            $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", '$1$2$3', $response);
            $xml = simplexml_load_string($xml);
            $json = json_encode($xml);
            $response_data = json_decode($json,true);
            $this->storeMidPayload($input["session_id"], json_encode($response_data));
            if(isset($response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['MCSTransactionID'])) {
                $input['gateway_id'] = $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['MCSTransactionID'] ?? null;

                $this->updateGatewayResponseData($input, $response_data);
            }
            if(isset($response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode']) && $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode'] == 0){
                $input['status'] = '1';
                $input['reason'] = 'Your payment has been successfully completed.';

            } else if(isset($response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode']) && $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode'] == 4){
                $input['status'] = '2';
                $input['reason'] = 'Your transaction is under process . Please wait for sometime!';

            } else if(isset($response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode']) && $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode'] == 1){
                $input['status'] = '0';
                $input['reason'] = $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultDetail'] ?? 'The transaction was unsuccessful.';

            } else if(isset($response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode']) && $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode'] == 2){
                $input['status'] = '0';
                $input['reason'] = $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultDetail'] ?? 'The transaction was unsuccessful.';
            } else {
                $input['status'] = '0';
                $input['reason'] = $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultDetail'] ?? 'The transaction was unsuccessful.';
            }
            return $input;
        }else{
            $this->storeMidPayload($input['session_id'], json_encode($inputData));
            \DB::table('transaction_session')->where('transaction_id', $input["session_id"])->update(["request_data"=>json_encode($inputData)]);
            $input['gateway_id'] = $input['order_id'] ?? '1';
            $this->updateGatewayResponseData($input, $inputData);
            return [
                'status' => '7',
                'reason' => "Please redirect to the specified 'payment_link' to complete the transaction processing.",
                'payment_link' => route('prismpay.form-request', $input['session_id'])
            ];
        }
    }

    public function getAuthenticationCode($check_assign_mid,$session_id){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => self::BASE_URL,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                //'Accept-Encoding: gzip, deflate, br',
                //'Connection: keep-alive',
                'Content-Type: application/json',
                // 'Accept: application/json',
                'ApiKey: '.$check_assign_mid->api_key,
                'AccountID: '.$check_assign_mid->account_id,
                'SessionID: '.$session_id
            ]
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response_data = json_decode($response, true);
        
        return $response_data;        
    }
    
    public function prismpayForm(Request $request, $transactionId){
        $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', $transactionId)
            ->first();
        if ($transaction_session == null) {
            return abort(404);
        }
        $check_assign_mid = checkAssignMID($transaction_session->payment_gateway_id);
        $input = json_decode($transaction_session->request_data, 1);
        $authentication = $this->getAuthenticationCode($check_assign_mid,$input['session_id']);
        \Log::info(['authentication'=>$authentication]);
        if(isset($authentication["Message"]) && !isset($authentication["jwt"])){
            $input['status'] = '0';
            $input['reason'] = $authentication["Message"] ?? 'The transaction was unsuccessful.';
            unset($input['reqest_datacardNo']);
            unset($input['reqest_datacardcvv']);
            unset($input['reqest_datacountrycode']);
            unset($input['request_countrycode']);
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        }else{
            $input['user_phone_no'] = str_replace(' ', '', $input['user_phone_no']);
            $input['user_phone_no'] = str_replace('+', '', $input['user_phone_no']);
            // dd($authentication);
            return view('gateway.prismpay.form', compact(['input', 'authentication']));
        }
    }

    public function getPrisampayData(Request $request){
        \Log::info(['request'=>$request->toArray()]);
        $input_json = TransactionSession::where('transaction_id', $request->sessionId)
            ->orderBy('id', 'desc')
            ->first();
        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => self::BASE_URL_PAYMENT,
        // CURLOPT_URL => 'https://prod.mycardstorage.com/api/api.asmx',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:myc="https://MyCardStorage.com/">
                <soapenv:Header>
                    <myc:AuthHeader>
                        <myc:ApiKey>'.$check_assign_mid->api_key.'</myc:ApiKey>
                        <myc:JWT>'.$request->txtJwt.'</myc:JWT>
                    </myc:AuthHeader>
                </soapenv:Header>
                <soapenv:Body>
                    <myc:CreditSale_Soap>
                        <myc:creditCardSale>
                            <myc:ServiceSecurity>
                                <myc:MCSAccountID>'.$check_assign_mid->account_id.'</myc:MCSAccountID>
                            </myc:ServiceSecurity>
                            <myc:TokenData>
                                <myc:CardHolderName>'.$request->cardHolderName.'</myc:CardHolderName>
                                <myc:CardNumber>'.$request->cardNumber.'</myc:CardNumber>
                                <myc:ExpirationMonth>'.$request->cardMonth.'</myc:ExpirationMonth>
                                <myc:ExpirationYear>'.$request->cardYear.'</myc:ExpirationYear>
                                <myc:CVV>'.$input['reqest_datacardcvv'].'</myc:CVV>
                                <myc:ThreeDSResponse>'.$request->idData3Ds.'</myc:ThreeDSResponse>
                            </myc:TokenData>
                            <myc:TransactionData>
                                <myc:Amount>'.$request->amount.'</myc:Amount>
                                <myc:CurrencyCode>USD</myc:CurrencyCode>
                            </myc:TransactionData>
                        </myc:creditCardSale>
                    </myc:CreditSale_Soap>
                </soapenv:Body>
            </soapenv:Envelope>',
        CURLOPT_HTTPHEADER => array(
                'Content-Type: text/xml',
                // 'User-Agent: PostmanRuntime/7.36.0',
                'Accept-Encoding:gzip, deflate, br',
                'Connection:keep-alive',
                'Accept:*/*'
            ),
        ));
        \Log::info(['post'=>'<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:myc="https://MyCardStorage.com/">
                <soapenv:Header>
                    <myc:AuthHeader>
                        <myc:ApiKey>'.$check_assign_mid->api_key.'</myc:ApiKey>
                        <myc:JWT>'.$request->txtJwt.'</myc:JWT>
                    </myc:AuthHeader>
                </soapenv:Header>
                <soapenv:Body>
                    <myc:CreditSale_Soap>
                        <myc:creditCardSale>
                            <myc:ServiceSecurity>
                                <myc:MCSAccountID>'.$check_assign_mid->account_id.'</myc:MCSAccountID>
                            </myc:ServiceSecurity>
                            <myc:TokenData>
                                <myc:CardHolderName>'.$request->cardHolderName.'</myc:CardHolderName>
                                <myc:CardNumber>'.$request->cardNumber.'</myc:CardNumber>
                                <myc:ExpirationMonth>'.$request->cardMonth.'</myc:ExpirationMonth>
                                <myc:ExpirationYear>'.$request->cardYear.'</myc:ExpirationYear>
                                <myc:CVV>'.$input['reqest_datacardcvv'].'</myc:CVV>
                                <myc:ThreeDSResponse>'.$request->idData3Ds.'</myc:ThreeDSResponse>
                            </myc:TokenData>
                            <myc:TransactionData>
                                <myc:Amount>'.$request->amount.'</myc:Amount>
                                <myc:CurrencyCode>USD</myc:CurrencyCode>
                            </myc:TransactionData>
                        </myc:creditCardSale>
                    </myc:CreditSale_Soap>
                </soapenv:Body>
            </soapenv:Envelope>']);
        $response = curl_exec($curl);
        $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", '$1$2$3', $response);
        $xml = simplexml_load_string($xml);
        $json = json_encode($xml);
        $response_data = json_decode($json,true);
         if(isset($response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['MCSTransactionID'])) {
            $input['gateway_id'] = $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['MCSTransactionID'] ?? null;

            $this->updateGatewayResponseData($input, $response_data);
        }
        \Log::info(['response_log'=>$response_data]);
        if(isset($response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode']) && $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode'] == 0)
            {

            $input['status'] = '1';
            $input['reason'] = 'Your payment has been successfully completed.';

        } else if(isset($response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode']) && $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode'] == 4){
            
            $input['status'] = '2';
            $input['reason'] = 'Your transaction is under process . Please wait for sometime!';

        } else if(isset($response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode']) && $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode'] == 1){

            $input['status'] = '0';
            $input['reason'] = $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultDetail'] ?? 'The transaction was unsuccessful.';

        } else if(isset($response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode']) && $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode'] == 2){

            $input['status'] = '0';
            $input['reason'] = $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultDetail'] ?? 'The transaction was unsuccessful.';
            
        } else {

            $input['status'] = '0';
            $input['reason'] = $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultDetail'] ?? 'The transaction was unsuccessful.';
        }
        unset($input['reqest_datacardNo']);
        unset($input['reqest_datacardcvv']);
        unset($input['reqest_datacountrycode']);
        unset($input['request_countrycode']);
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function prisampayFail(Request $request){
        \Log::info(['request_fail'=>$request->toArray()]);
        $input_json = TransactionSession::where('transaction_id', $request->sessionId)
            ->orderBy('id', 'desc')
            ->first();
        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        $errorData = json_decode($request->idData3Ds);
        $input['status'] = '0';
        $input['reason'] = $errorData->error ?? 'The transaction was unsuccessful.';
        unset($input['reqest_datacardNo']);
        unset($input['reqest_datacardcvv']);
        unset($input['reqest_datacountrycode']);
        unset($input['request_countrycode']);
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }


    public function redirect($id)
    {
        $transaction = DB::table('transaction_session')
            ->select("id", "payment_gateway_id", "request_data")
            ->where("transaction_id", $id)
            ->first();

        if (empty($transaction)) {
            abort(404);
        }

        $input = json_decode($transaction->request_data, true);
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

        // call the status API
        $status_data = $this->statusAPI($input, $check_assign_mid);

        // success
        if (
            isset($status_data['status']) && $status_data['status'] == true &&
            isset($status_data['transaction_status']) && $status_data['transaction_status'] == 'SUCCESSFUL'
        ) {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction processed successfully.';
            // pending
        } elseif (
            isset($status_data['status']) && $status_data['status'] == true &&
            isset($status_data['transaction_status']) && $status_data['transaction_status'] == 'PENDING'
        ) {
            $input['status'] = '2';
            $input['reason'] = 'Your transaction is under process . Please wait for sometime!';
            // failed
        } elseif (
            isset($status_data['status']) && $status_data['status'] == true &&
            isset($status_data['transaction_status']) && $status_data['transaction_status'] == 'FAILED'
        ) {
            $input['status'] = '0';
            $input['reason'] = $status_data['message'] ?? $status_data['transaction_message'] ?? $status_data['exception'] ?? 'Transaction failed to pass 3DS.';
        } else {
            \Log::info(['kpentag-return-status' => $status_data]);
            $input['status'] = '0';
            $input['reason'] = $status_data['message'] ?? $status_data['transaction_message'] ?? $status_data['exception'] ?? 'Transaction failed to pass 3DS.';
        }

        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function webhook($id)
    {
        $transaction = DB::table('transaction_session')
            ->select("id", "payment_gateway_id", "request_data")
            ->where("transaction_id", $id)
            ->first();

        if (empty($transaction)) {
            abort(404);
        }

        http_response_code(200);

        $input = json_decode($transaction->request_data, true);
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

        // call the status API
        $status_data = $this->statusAPI($input, $check_assign_mid);

        // success
        if (
            isset($status_data['status']) && $status_data['status'] == true &&
            isset($status_data['transaction_status']) && $status_data['transaction_status'] == 'SUCCESSFUL'
        ) {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction processed successfully.';
            // pending
        } elseif (
            isset($status_data['status']) && $status_data['status'] == true &&
            isset($status_data['transaction_status']) && $status_data['transaction_status'] == 'PENDING'
        ) {
            $input['status'] = '2';
            $input['reason'] = 'Your transaction is under process . Please wait for sometime!';
            // failed
        } elseif (
            isset($status_data['status']) && $status_data['status'] == true &&
            isset($status_data['transaction_status']) && $status_data['transaction_status'] == 'FAILED'
        ) {
            $input['status'] = '0';
            $input['reason'] = $status_data['message'] ?? $status_data['transaction_message'] ?? $status_data['exception'] ?? 'Transaction failed to pass 3DS.';
        } else {
            \Log::info(['kpentag-return-status' => $status_data]);
            $input['status'] = '0';
            $input['reason'] = $status_data['message'] ?? $status_data['transaction_message'] ?? $status_data['exception'] ?? 'Transaction failed to pass 3DS.';
        }

        $this->storeTransaction($input);
        exit();
    }

    public function statusAPI($input, $check_assign_mid)
    {
        $status_url = self::BASE_URL . '/client/transaction/status_check/' . $input['session_id'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $status_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_body = curl_exec($ch);
        curl_close($ch);

        return json_decode($response_body, true);
    }
}
