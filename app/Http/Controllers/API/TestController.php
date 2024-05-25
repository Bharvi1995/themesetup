<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Transaction;
use DB;
use Carbon\Carbon;
use App\User;
use App\MIDDetail;
use App\Application;
use App\AutoReports;
use App\AutoReportsChild;
use Dompdf\Dompdf;
use Dompdf\Options;
class TestController extends Controller
{

    public function __construct()
    {
        $this->MIDDetail = new MIDDetail;
        $this->AutoReports = new AutoReports;
        $this->AutoReportsChild = new AutoReportsChild;
        $this->User = new User;
        $this->Application = new Application;
    }

    function requestAuthToken() {
        $url = "https://secure.transactworld.com/transactionServices/REST/v1/authToken";
        $data = "authentication.partnerId=4" .
        "&merchant.username=testpay, option, facility)" .
        "&authentication.sKey=gdLgy6ymBV3pIOh1RBAv2HWrV82iaJyy";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if(curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return $responseData;
    }

    public function indextransaction(){
        $token = $this->requestAuthToken();
        $arrToken = json_decode($token);
        echo $arrToken->AuthToken."<br>";
        echo "<pre>";print_r($arrToken);
        $values= "15083|gdLgy6ymBV3pIOh1RBAv2HWrV82iaJyy|Transaction03|2.00";
        $generatedCheckSum=md5($values);
        echo $generatedCheckSum;exit();
    }

    public function checkTransaction(){
        $url = "https://secure.transactworld.com/transactionServices/REST/v1/payments";
        $data = "authentication.memberId=15083" .
            "&authentication.checksum=e51b745f795e271c58676dba3e1eaf5f" .
            "&authentication.terminalId=16770" .
            "&merchantTransactionId=Transaction03" .
            "&amount=2.00" .
            "&currency=EUR" .
            "&orderDescriptor=Test Transaction" .
            "&shipping.country=UK" .
            "&shipping.city=Aston" .
            "&shipping.state=NA" .
            "&shipping.postcode=CH5 3LJ" .
            "&shipping.street1=19 Scrimshire Lane" .
            "&customer.telnocc=+44" .
            "&customer.phone=07730432996" .
            "&customer.email=bharvi@yopmail.com" .
            "&customer.givenName=John" .
            "&customer.surname=Doe" .
            "&customer.ip=49.36.65.28" .
            "&customer.birthDate=19890202" .
            "&card.number=4304636301077885" .
            "&card.expiryMonth=01" .
            "&card.expiryYear=2022" .
            "&card.cvv=936" .
            "&paymentBrand=VISA" .
            "&paymentMode=CC" .
            "&paymentType=DB" .
            "&merchantRedirectUrl=https://webhook.site/b2e9fd9e-cc8f-4dc0-8fcb-4b6f831ce385" .
            "&notificationUrl=https://webhook.site/b2e9fd9e-cc8f-4dc0-8fcb-4b6f831ce385" .
            "&tmpl_amount=2.00" .
            "&tmpl_currency=EUR" .
            "&recurringType=INITIAL" .
            "&createRegistration=true".
            "&attemptThreeD=Only3D".
            "&customer.customerId=12345";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('AuthToken:eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJQYXlwb3VuZCIsInJvbGUiOiJtZXJjaGFudCIsImlzcyI6IlBaIiwiZXhwIjoxNjQxOTg1Njc3fQ.RvZ6Io7NroF3zLFUXnoQUZeJakQS4-59FiHb61OGn2E'));
        $responseData = curl_exec($ch);
        if(curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        echo "<pre>";print_r($responseData);exit();
        return $responseData;
    }

    public function checkout_transaction(){
        return view("gateway.test");
    }

    public function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
    public function aesencrypt($input, $key) {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input = $this->pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = strtoupper(bin2hex($data));
        return $data;
    }

    function fnEncrypt($sValue, $sSecretKey) {
        return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $sSecretKey, $sDecrypted, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }

    function fnDecrypt($sValue, $sSecretKey) {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $sSecretKey, base64_decode($sEncrypted), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    }

    public function encryptData($value){
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'ygjH44Nkbgf8CVX9';
        $secret_iv = 'ygjH44Nkbgf8CVX9';
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $iframe_json = openssl_encrypt(base64_decode($value), $encrypt_method, $key, 0, $iv);
        return $iframe_json;
    }

    public function tests2s(){
        return view("gateway.test");
    }

    public function test(){
        $ShopId = "30004";
        $password = "61e83346c5869";

        $Account_id = "2004";
        $key = "91ba08310121830352290b83ec1268fc";

        $orderId = uniqid();
        $signature = hash("sha256", $orderId.$key); 

        $postData=
        '{
          "Credentials": {
            "AccountId": "' . $Account_id . '",
            "Signature": "'. $signature .'"
          },
          "CustomerDetails": {
            "FirstName": "John",
            "LastName": "Smith",
            "CustomerIP": "42.105.166.172",
            "Phone": "99894511",
            "Email": "customer.email@email.com",
            "Street": "Oxford",
            "City": "London",
            "Region": "",
            "Country": "GB",
            "Zip": "LND-032"
          },
          "CardDetails": {
            "CardHolderName": "John Smith",
            "CardNumber": "4111111111111111",
            "CardExpireMonth": "05",
            "CardExpireYear": "22",
            "CardSecurityCode": "264"
          },
          "ProductDescription": "Tv Product",
          "TotalAmount": "1300",
          "CurrencyCode": "EUR",
          "TransactionId": "' . $orderId . '",
          "CallbackURL": "https://webhook.site/b2e9fd9e-cc8f-4dc0-8fcb-4b6f831ce385",
          "ReturnUrl": "https://webhook.site/b2e9fd9e-cc8f-4dc0-8fcb-4b6f831ce385",
          "Custom": "var=' . $orderId . '&var1=321&var3=456"
        }';
        echo $postData."<br>";//exit();                      
        $auth= base64_encode("$ShopId:$password");
        echo $auth;
          $curl = curl_init();
          curl_setopt_array($curl, array(
          CURLOPT_URL => "https://sandbox.kapopay.com/process/payment/",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_FOLLOWLOCATION  => false,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $postData,
          CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "authorization: Basic $auth",
            "content-type: application/json"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);  
        curl_close($curl);
        echo "<pre>";print_r($response);exit();
        //Convert json to Array
        $responseData = json_decode($response,true);
        
        
        
    }


    public function invoice(){
            
        $service_url = 'https://payments.anmgw.com/third_party_request';
          $service_id = "1647";
          $client_key = "n+1jZA3LGC0VadonWmxVzFcudc4vaVtcjLODovgJz9nhGILdsR47FVvVBEp2OB6+V7vKPo4OVVzkN32BKHe68A==";
          $secret_key = "Nls5dTuNeJuokWcT/5ywKH63GBO9D/QNt43jQ0pm3GvgY906QmOp5NABWbs4BcFjmUMkyCjlNDNHYOYTU/xJ9A==";

          //$data = array('service_id' => $service_id, 'trans_type' => 'BLC');
          $data = array(
            'nickname' => 'Test',
            'amount' => '100',
            'exttrid' =>"1632914669ZKTCERPX8E",
            'reference' => 'ZMVA1632914669',
            'callback_url' => 'http://localhost:8000/appsnmobile/callback/ZMVA1632914669',
            'service_id' => "1647",
            'ts' => date('Y-m-d H:i:s'),
            'landing_page' => '',
            'payment_mode' => 'CRD',
            'currency_code' => 'USD',
            'currency_val' => '100'
          );
          $data_string = json_encode($data);
          $signature =  hash_hmac ( 'sha256' , $data_string , $secret_key );
          $auth = $client_key.':'.$signature;

          $ch = curl_init($service_url);
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");   
          curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string); 
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_HTTPHEADER, array(
              'Authorization: '.$auth,
              'Content-Type: application/json',
              'timeout: 180',
              'open_timeout: 180'
              )
          );

          $result = curl_exec($ch);
          echo $result;
          exit();


        $request_url = "https://api.nowpayments.io/v1/invoice";
        $request_data = [
                "price_amount"=>"1000",
                "price_currency"=>"USD",
                "order_id"=>"RGDBP-21314",
                "order_description"=>"Apple Macbook Pro 2019 x 1",
                "ipn_callback_url"=>"https://testpay.com",
                "success_url"=>"https://testpay.com",
                "cancel_url"=>"https://testpay.com"
            ];
        $request_headers = ["x-api-key: PN6QNBH-QAE40NE-PH0SHBW-KV3GXP1"];
        
        $payload = json_encode($request_data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request_data, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        $response_body = curl_exec($ch);
        echo "<pre>";print_r($response_body);exit();
        curl_close ($ch);
    }

    public function onlinenaira(){
        return view('testing.onlinenaira.index');
    }
        
    public function autoReport(){
        $week_3Ago=\Carbon\Carbon::now()->subWeeks(3);
        $start_date=$week_3Ago->startOfWeek();
        $week_2Ago=\Carbon\Carbon::now()->subWeeks(3);
        $end_date = $week_2Ago->endOfWeek();
        $chargebacks_start_date = $start_date;
        $chargebacks_end_date = Carbon::yesterday();
        $allRates=getAllCurrenciesRates();
        User::with('application')
            ->with(array('transactions'=>function($trans) use($start_date,$end_date){
                $trans->whereBetween('transaction_date',[$start_date,$end_date]);
            }))
            ->whereHas('transactions',function($transactions) use($start_date,$end_date){
                $transactions
                //->whereNotIn('payment_gateway_id',['1','2'])
                ->whereBetween('transaction_date',[$start_date,$end_date]);
            })
            ->chunk(100,function($users) use($start_date,$end_date,$chargebacks_start_date,$chargebacks_end_date,$allRates){
                //echo "<pre>";//print_r($users);exit();
                if($users){
                    
                    $childDatas=array();
                    $userIds=array();
                    foreach($users as $user){
                        $data = [];
                        $userId = $user->id;
                        $payout_report = AutoReports::where('user_id', $userId)->orderBy("id", "DESC")->first();
                        $data['user_id'] = $user->id;
                        $data['date'] = date('Y-m-d', time());
                        $data['processor_name'] = 'testpay';
                        $data['company_name'] = $user->application->business_name;
                        $data['address'] = '';
                        $data['phone_no'] = $user->application->phone_no;
                        $data['start_date'] = date('Y-m-d', strtotime($start_date));
                        $data['end_date'] = date('Y-m-d', strtotime($end_date));
                        $data['chargebacks_start_date'] = date('Y-m-d', strtotime($chargebacks_start_date));
                        $data['chargebacks_end_date'] = date('Y-m-d', strtotime($chargebacks_end_date));
                        $data['merchant_discount_rate'] = $user->merchant_discount_rate; //Crerdit
                        $data['rolling_reserve_paercentage'] = $user->rolling_reserve_paercentage;
                        $data['transaction_fee_paercentage'] = $user->transaction_fee;
                        //$data['declined_fee_amount'] = $user->transaction_fee;
                        $data['refund_fee_paercentage'] = $user->refund_fee;
                        $data['chargebacks_fee_paercentage'] = $user->chargeback_fee;
                        $data["flagged_fee_paercentage"] = $user->flagged_fee;
                        $data["retrieval_fee_paercentage"] = $user->retrieval_fee;
                        $data['wire_fee'] = 50; // 50
                        $data['invoice_no'] = date('Y').rand(100,999).time();
                        $data['genereted_by'] = 'User';
                        $show_client_side = '0';
                        $data['show_client_side'] = $show_client_side;
                        $report = $this->AutoReports->storeData($data);
                        $currencyArray = \DB::table('transactions')->where('user_id', $userId)
                                    ->whereNotIn('payment_gateway_id', ['1','2'])
                                    ->where(\DB::raw('DATE(transactions.transaction_date)'), '>=', $start_date)
                                    ->where(\DB::raw('DATE(transactions.transaction_date)'), '<=', $end_date)
                                    ->groupBy("transactions.currency")->pluck("currency")->toArray();
                        $report_id=$report->id;
                        foreach ($currencyArray as $key => $value) {
                            $chekTransactionInCurrency = \DB::table('transactions')
                                ->where('user_id', $userId)
                                ->where('currency', $value)
                                ->where(\DB::raw('DATE(transactions.transaction_date)'), '>=', $start_date)
                                ->where(\DB::raw('DATE(transactions.transaction_date)'), '<=', $end_date)
                                ->count();
                            if ($chekTransactionInCurrency > 0) {
                                $approved_transaction = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
                                    ->where('user_id', $userId)
                                    ->where('currency', $value)
                                    ->where('status', '1')
                                    ->whereNotIn('payment_gateway_id', ['1','2'])
                                    ->where(\DB::raw('DATE(transactions.transaction_date)'), '>=', $start_date)
                                    ->where(\DB::raw('DATE(transactions.transaction_date)'), '<=', $end_date)
                                    ->first();
                                $declined_transaction = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
                                    ->where('user_id', $userId)
                                    ->where('currency', $value)
                                    ->where('status', '0')
                                    ->whereNotIn('payment_gateway_id', ['1','2'])
                                    ->where(\DB::raw('DATE(transactions.transaction_date)'), '>=', $start_date)
                                    ->where(\DB::raw('DATE(transactions.transaction_date)'), '<=', $end_date)
                                    ->first();
                                $chargebacks_transaction = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
                                    ->where('user_id', $userId)
                                    ->where('currency', $value)
                                    ->whereNotIn('payment_gateway_id', ['1','2'])
                                    ->where('chargebacks', '1')->where('chargebacks_remove', '0')
                                    ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '>=', $chargebacks_start_date)
                                    ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '<=', $chargebacks_end_date)
                                    ->first();
                                $refund_transaction = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
                                    ->where('user_id', $userId)
                                    ->where('currency', $value)
                                    ->whereNotIn('payment_gateway_id', ['1','2'])
                                    ->where('refund', '1')->where('chargebacks', "0")->where('refund_remove', '0')
                                    ->where(\DB::raw('DATE(transactions.refund_date)'), '>=', $chargebacks_start_date)
                                    ->where(\DB::raw('DATE(transactions.refund_date)'), '<=', $chargebacks_end_date)
                                    ->first();
                                $total_flagged = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
                                    ->where('user_id', $userId)
                                    ->where('currency', $value)
                                    ->whereNotIn('payment_gateway_id', ['1','2'])
                                    ->where('is_flagged', '1')->where("is_flagged_remove", "0")->where("chargebacks", "0")
                                    ->where(\DB::raw('DATE(transactions.flagged_date)'), '>=', $chargebacks_start_date)
                                    ->where(\DB::raw('DATE(transactions.flagged_date)'), '<=', $chargebacks_end_date)
                                    ->first();
                                $total_retrieval = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
                                    ->where('user_id', $userId)
                                    ->where('currency', $value)
                                    ->whereNotIn('payment_gateway_id', ['1','2'])
                                    ->where('is_retrieval', '1')->where('chargebacks', "0")->where('is_retrieval_remove', '0')
                                    ->where(\DB::raw('DATE(transactions.retrieval_date)'), '>=', $chargebacks_start_date)
                                    ->where(\DB::raw('DATE(transactions.retrieval_date)'), '<=', $chargebacks_end_date)
                                    ->first();
                                $total_past_flagged = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
                                    ->where('user_id', $userId)
                                    ->where('currency', $value)
                                    ->whereNotIn('payment_gateway_id', ['1','2'])
                                    ->where('is_flagged_remove', '1')
                                    ->where(\DB::raw('DATE(transactions.flagged_remove_date)'), '>=', $chargebacks_start_date)
                                    ->where(\DB::raw('DATE(transactions.flagged_remove_date)'), '<=', $chargebacks_end_date)
                                    ->first();
                                $total_past_retrieval = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
                                    ->where('user_id', $userId)
                                    ->where('currency', $value)
                                    ->whereNotIn('payment_gateway_id', ['1','2'])
                                    ->where('is_retrieval_remove', '1')
                                    ->where(\DB::raw('DATE(transactions.retrieval_remove_date)'), '>=', $chargebacks_start_date)
                                    ->where(\DB::raw('DATE(transactions.retrieval_remove_date)'), '<=', $chargebacks_end_date)
                                    ->first();
                                $childData['user_id'] = $userId;
                                $childData['autoreport_id'] = $report_id;
                                $childData["total_transaction_count"] =  $approved_transaction->count + $declined_transaction->count;
                                $childData["total_transaction_sum"] = $approved_transaction->amount + $declined_transaction->amount;
                                $childData['approve_transaction_count'] = $approved_transaction->count;
                                $childData['approve_transaction_sum'] = $approved_transaction->amount;
                                $childData['declined_transaction_count'] = $declined_transaction->count;
                                $childData['declined_transaction_sum'] = $declined_transaction->amount;
                                $childData['chargeback_transaction_count'] = $chargebacks_transaction->count;
                                $childData['chargeback_transaction_sum'] = $chargebacks_transaction->amount;
                                $childData['refund_transaction_count'] = $refund_transaction->count;
                                $childData['refund_transaction_sum'] = $refund_transaction->amount;
                                $childData['flagged_transaction_count'] = $total_flagged->count;
                                $childData['flagged_transaction_sum'] = $total_flagged->amount;
                                $childData['retrieval_transaction_count'] = $total_retrieval->count;
                                $childData['retrieval_transaction_sum'] = $total_retrieval->amount;
                                $childData['currency'] = $value;
                                $childData['mdr'] = ($user->merchant_discount_rate * $approved_transaction->amount) / 100;
                                $childData['rolling_reserve'] = ($user->rolling_reserve_paercentage * $approved_transaction->amount) / 100;

                                $transactionFee = ($user->transaction_fee * ($approved_transaction->count + $declined_transaction->count));
                                $transactionFeeConvertedAmount = 0;
                                if ($transactionFee != 0) {
                                    $returnFee = checkSelectedCurrencyTwo('USD', $transactionFee, $value);
                                    $transactionFeeConvertedAmount = $returnFee["amount"];
                                }
                                $childData['transaction_fee'] = $transactionFee;
                                $childData['refund_fee'] = ($user->refund_fee * $refund_transaction->count);
                                $chargebacks_fee = ($user->chargeback_fee * $chargebacks_transaction->count);
                                $chargebackFeeConvertedAmount = 0;
                                if ($chargebacks_fee != 0) {
                                    $chargebackFee = checkSelectedCurrencyTwo('USD', $chargebacks_fee, $value);
                                    $chargebackFeeConvertedAmount = $chargebackFee["amount"];
                                }
                                $childData['chargeback_fee'] = $chargebacks_fee;
                                $flagged_fee = ($user->flagged_fee * $total_flagged->count);
                                $flaggedFeeConvertedAmount = 0;
                                if ($flagged_fee != 0) {
                                    $flaggedReturnFee = checkSelectedCurrencyTwo('USD', $flagged_fee, $value);
                                    $flaggedFeeConvertedAmount = $flaggedReturnFee["amount"];
                                }
                                $childData['flagged_fee'] = $flagged_fee;
                                $retrieval_fee = ($user->retrieval_fee * $total_retrieval->count);
                                $retrievalFeeConvertedAmount = 0;
                                if ($retrieval_fee != 0) {
                                    $retrievalReturnFee = checkSelectedCurrencyTwo('USD', $retrieval_fee, $value);
                                    $retrievalFeeConvertedAmount = $retrievalReturnFee["amount"];
                                }
                                $childData['retrieval_fee'] = $retrieval_fee;
                                $childData["remove_past_flagged"] = $total_past_flagged->count;
                                $past_flagged_fee = ($user->flagged_fee * $total_past_flagged->count);
                                $pastFlaggedFeeConvertedAmount = 0;
                                if ($past_flagged_fee != 0) {
                                    $pastFlaggedFee = checkSelectedCurrencyTwo('USD', $past_flagged_fee, $value);
                                    $pastFlaggedFeeConvertedAmount = $pastFlaggedFee["amount"];
                                }
                                $childData["past_flagged_charge_amount"] = $past_flagged_fee;

                                $past_flagged_sum_deduction = (($user->merchant_discount_rate * $total_past_flagged->amount) / 100) + (($user->rolling_reserve_paercentage * $total_past_flagged->amount) / 100);
                                $finalPastFalggedAmount = 0;
                                if ($total_past_flagged->amount != 0) {
                                    $finalPastFalggedAmount = ($total_past_flagged->amount) - $past_flagged_sum_deduction - ($convertTransactionFee * $total_past_flagged->count);
                                }
                                $childData["past_flagged_sum"] = $finalPastFalggedAmount;
                                $childData["remove_past_retrieval"] = $total_past_retrieval->count;
                                $past_retrieval_charge_amount = ($user->retrieval_fee * $total_past_retrieval->count);
                                $pastRetrievalAmount = 0;
                                if ($past_retrieval_charge_amount != 0) {
                                    $pastRetrievalFee = checkSelectedCurrencyTwo('USD', $past_retrieval_charge_amount, $value);
                                    $pastRetrievalAmount = $pastRetrievalFee["amount"];
                                }
                                $childData["past_retrieval_charge_amount"] = $past_retrieval_charge_amount;

                                $past_retrieval_sum_deduction = (($user->merchant_discount_rate * $total_past_retrieval->amount) / 100) + (($user->rolling_reserve_paercentage * $total_past_retrieval->amount) / 100);
                                $finalPastRetrievalAmount = 0;
                                if ($total_past_retrieval->amount != 0) {
                                    $finalPastRetrievalAmount = ($total_past_retrieval->amount) - $past_retrieval_sum_deduction - ($convertTransactionFee * $total_past_retrieval->count);
                                }
                                $childData["past_retrieval_sum"] = $finalPastRetrievalAmount;
                                $returnFlaggedFee = 0;
                                $totalChargebackAmount = 0;
                                $totalChargebackCount = 0;
                                if (isset($payout_report)) {
                                    $payout_start_date = date('Y-m-d', strtotime($payout_report->start_date));
                                    $payout_end_date = date('Y-m-d', strtotime($payout_report->end_date));
                                    $checkedPastFlagged = \DB::table('transactions')
                                        ->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
                                        ->where('user_id', $userId)
                                        ->where('currency', $value)->where(["is_flagged" => "1", "chargebacks" => "1"])
                                        ->where(\DB::raw('DATE(transactions.flagged_date)'), '>=', $payout_start_date)
                                        ->where(\DB::raw('DATE(transactions.flagged_date)'), '<=', $payout_end_date)
                                        ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '>=', $chargebacksStartDate)
                                        ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '<=', $chargebacksEndDate)
                                        ->whereNotIn('payment_gateway_id', ['1','2'])
                                        ->first();
                                    $pastFlaggedChargebackAmount = 0;
                                    if ($checkedPastFlagged->amount != 0) {
                                        $pastFlaggedChargebackAmount = ($checkedPastFlagged->amount) - (($user->merchant_discount_rate * $checkedPastFlagged->amount) / 100) - (($user->rolling_reserve_paercentage * $checkedPastFlagged->amount) / 100) - ($convertTransactionFee * $checkedPastFlagged->count);
                                    }
                                    $totalChargebackCount += $checkedPastFlagged->count;
                                    $checkedPastRefund = \DB::table('transactions')
                                        ->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
                                        ->where('user_id', $userId)
                                        ->where('currency', $value)->where(["refund" => "1", "chargebacks" => "1"])
                                        ->where(\DB::raw('DATE(transactions.refund_date)'), '>=', $payout_start_date)
                                        ->where(\DB::raw('DATE(transactions.refund_date)'), '<=', $payout_end_date)
                                        ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '>=', $chargebacksStartDate)
                                        ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '<=', $chargebacksEndDate)
                                        ->whereNotIn('payment_gateway_id', ['1','2'])
                                        ->first();
                                    $pastRefundChargebackAmount = 0;
                                    if ($checkedPastRefund->amount != 0) {
                                        $pastRefundChargebackAmount = ($checkedPastRefund->amount) - (($user->merchant_discount_rate * $checkedPastRefund->amount) / 100) - (($user->rolling_reserve_paercentage * $checkedPastRefund->amount) / 100) - ($convertTransactionFee * $checkedPastRefund->count);
                                    }
                                    $totalChargebackCount += $checkedPastRefund->count;
                                    $checkedPastRetrieval = \DB::table('transactions')
                                        ->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
                                        ->where('user_id', $userId)
                                        ->where('currency', $value)->where(["is_retrieval" => "1", "chargebacks" => "1"])
                                        ->where(\DB::raw('DATE(transactions.retrieval_date)'), '>=', $payout_start_date)
                                        ->where(\DB::raw('DATE(transactions.retrieval_date)'), '<=', $payout_end_date)
                                        ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '>=', $chargebacksStartDate)
                                        ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '<=', $chargebacksEndDate)
                                        ->whereNotIn('payment_gateway_id', ['1','2'])
                                        ->first();
                                    $pastRetrievalChargebackAmount = 0;
                                    if ($checkedPastRetrieval->amount != 0) {
                                        $pastRetrievalChargebackAmount = ($checkedPastRetrieval->amount) - (($user->merchant_discount_rate * $checkedPastRetrieval->amount) / 100) - (($user->rolling_reserve_paercentage * $checkedPastRetrieval->amount) / 100) - ($convertTransactionFee * $checkedPastRetrieval->count);
                                    }
                                    $totalChargebackCount += $checkedPastRetrieval->count;
                                    $totalChargebackAmount = $pastRetrievalChargebackAmount + $pastRefundChargebackAmount + $pastFlaggedChargebackAmount;
                                    $returnFlaggedFee = ($user->flagged_fee * $checkedPastFlagged->count) + ($user->refund_fee * $checkedPastRefund->count) + ($user->retrieval_fee * $checkedPastRetrieval->count);
                                }
                                $returnFeeAmount = 0;
                                if ($returnFlaggedFee != 0) {
                                    $returnFee = checkSelectedCurrencyTwo('USD', $returnFlaggedFee, $value);
                                    $returnFeeAmount = $returnFee["amount"];
                                }
                                $totalFee = $chargebackFeeConvertedAmount + $flaggedFeeConvertedAmount + $retrievalFeeConvertedAmount  + $transactionFeeConvertedAmount;
                                $childData['return_fee'] = $totalChargebackAmount;
                                $childData['return_fee_count'] = $totalChargebackCount;
                                $childData["past_flagged_fee"] = $returnFeeAmount;
                                $childData["transactions_fee_total"] = $totalFee;
                                $childData['sub_total'] = $approved_transaction->amount - ($refund_transaction->amount + $chargebacks_transaction->amount + $total_flagged->amount + $total_retrieval->amount);
                                $childData['net_settlement_amount'] = $childData['sub_total'] - ($totalFee + $childData['rolling_reserve'] + $childData['mdr']) + $childData["past_flagged_sum"] + $childData["past_retrieval_sum"] + $returnFeeAmount + $totalChargebackAmount;
                                $this->AutoReportsChild->storeData($childData);
                            }
                        }
                        
                    }
                }
            });
    }

  //   public function storeRandom(Request $request){
		// $input["first_name"] = "test";
		// $input["last_name"] = "last";
		// $input["address"] = "Address";
		// $input["customer_order_id"] = "";
		// $input["country"] = "IN";
		// $input["state"] ="Gujarat";
		// $input["city"] = "Ahmd";
		// $input["zip"] ="380015";
		// $input["email"] = "test@yopmail.com";
		// $input["phone_no"] = "9876543212";
		// $input["card_type"] ="2";
		// $input["amount"] = "150";
		// $input["currency"] = "GBP";
		// $input["order_id"] = "2021489411010111657";
		// $input["user_id"] = "1";
		// $input["status"] = "1";
		// $input["gateway_id"] = '1';
		// $input["payment_gateway_id"] = '3';
		// $input["transaction_date"] = date("Y-m-d");
		// $input["created_at"] = date("Y-m-d H:i:s");
		// $input["updated_at"] = date("Y-m-d H:i:s");
		// $input["card_no"] = "4242424242424242";
		// //$input["transaction_date"] = date("Y-m-d",strtotime('tomorrow'));
		// DB::table("transactions")->insert($input);
  //   }


    public function getBillTransactions(){
    	//$t=time();
        $curl = curl_init();
        $apiKey = '10a2bc3c-b60b-4460-816c-be150f11362d';
        $apiSecret = '8e10f72a-5929-407d-aed4-a469000e3602';
        $timenow = round(microtime(true));
        $params = [
            "destinationCurrency" => $input["currency"],
            "orderId" => $input["order_id"],
            "price" => $input["amount"]
        ];
        $post = json_encode($params);
        $hash = hash_hmac("sha512", $apiKey.$timenow.$post, $apiSecret);
        $request_headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'API-Key: '.$apiKey,
            'API-Hash: '.$hash,
            'operation-id:78539fe0-e9b0-4e4e-8c86-70b36aa93d4f',
            'Request-Timestamp: '.$timenow
        ];
        $header = [];
        curl_setopt_array($curl, [
          CURLOPT_URL => "https://api.bitbaypay.com/rest/bitbaypay/payments",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_HTTPHEADER => $request_headers,
          CURLOPT_POSTFIELDS => $post,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          echo $response;
        }
        exit();
        $request_url ="https://www.onlinenaira.com/process-sandbox.htm";
        $request_data = [
            "member" => "bitmatix",
            "action" => "payment",
            "price" => "100",
            'country' => "NGN",
            'apikey' => 'neiD1DcwX9LiMl1liUNh',
            'product' => 'asdasdas',
            'ureturn' => 'https://www.google.com/',
            'unotify' => 'https://www.google.com/',
            'ucancel' => 'https://www.google.com/',
            'comments' => 'test',
        ];

        $payload = json_encode($request_data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        $response_body = curl_exec($ch);
        echo "<pre>";print_r($response_body);exit();
        curl_close ($ch);
     //    $response_data = json_decode($response_body, 1);
        //iPayBill Integration 

    	// $midNo = "92082";
    	// $gatewayNo = "92082001";
    	// $orderNo = "20299845673450";
    	// $currency = "USD";
    	// $amount =  100;
    	// $cardNo = "5555555555554444";
    	// $ccExpiryYear = "2024";
    	// $ccExpiryMonth ="12";
    	// $cvvNumber = "123";
    	// $key = "PVLH6p2V";
    	// $signSrc = $midNo.$gatewayNo.$orderNo.$currency.$amount.$cardNo.$ccExpiryYear.$ccExpiryMonth.$cvvNumber.$key;
     //    $signInfo = hash('sha256', trim($signSrc));
     //    $url = "https://secure.ipaybill.com/TestTPInterface";
    	// $request_data = [
    	// 	"merNo" => $midNo,
    	// 	"gatewayNo" => $gatewayNo,
    	// 	"orderNo" => $orderNo,
    	// 	"orderCurrency" => $currency,
    	// 	"orderAmount" => $amount,
    	// 	"shipFee" => "10.00",
    	// 	"cardNo" => $cardNo,
    	// 	"cardExpireMonth" => $ccExpiryMonth,
    	// 	"cardExpireYear" => $ccExpiryYear,
    	// 	'cardSecurityCode' =>$cvvNumber,
    	// 	"firstName" => "Test",
    	// 	"lastName" => "T",
    	// 	"email" => "test@gmail.com",
    	// 	"ip" => "192.168.1.10",
    	// 	"phone" => "09876578987",
    	// 	"country" => "US",
    	// 	"city" => "test",
    	// 	"address" => "teste",
    	// 	"zip" => "234567",
    	// 	"webSite" => "www.google.com",
    	// 	'signInfo' => $signInfo
    	// ];
    	// $curl = curl_init(); 
     //    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
     //    curl_setopt($curl, CURLOPT_URL, $url);
     //    curl_setopt($curl, CURLOPT_POST, 1);
     //    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($request_data, '', '&'));
     //    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
     //    $result = curl_exec($curl);
    	//Opay integration
    	// $request_url = "http://sandbox-cashierapi.opayweb.com/api/v3/transaction/initialize";
    	// $request_headers = [
     //        'Content-Type: application/json',
     //        'Authorization: Bearer OPAYPUB16255504247080.44103725950103456',
     //        'MerchantId: 256621070626326',
     //    ];
     //    $request_data = [
     //        "reference" => '202998765432',
     //        "amount" => 10,
     //        "currency" => 'NGN',
     //        "country" => 'NG',
     //        "payType" => 'bankcard',
     //        "firstName" => 'Test',
     //        "lastName" => 'last',
     //        "customerEmail" => 'test@yopmail.com',
     //        "customerPhone" => '987654322',
     //        "cardNumber" => '4242424242424242',
     //        "cardDateMonth" => '12',
     //        "cardDateYear" => substr('2024', -2),
     //        "cardCVC" => '123',
     //        "bankAccountNumber" => null,
     //        "bankCode" => null,
     //        "billingZip" => '67890',
     //        "billingCity" => 'Test',
     //        "billingAddress" => 'address',
     //        "billingState" => 'state',
     //        "billingCountry" => 'country',
     //        "return3dsUrl" => 'https://www.google.com/',
     //        "reason" => 'company_name',
     //    ];
        
     //    ksort($request_data);
     //    echo "<pre>";print_r($request_data);exit();
     //    $payload = json_encode($request_data);
     //    $ch = curl_init();
     //    curl_setopt($ch, CURLOPT_URL, $request_url);
     //    curl_setopt($ch, CURLOPT_POST, 1);
     //    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
     //    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     //    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
     //    $response_body = curl_exec($ch);
     //    curl_close ($ch);
     //    $response_data = json_decode($response_body, 1);
     //    echo "<pre>";print_r($response_data);exit();
    	//echo http_build_query($request_data, '', '&');
    	//echo "<pre>";print_r($request_data);exit();
    	// $curl = curl_init();
     //    curl_setopt($curl, CURLOPT_URL, $url);
     //    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
     //    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
     //    //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
     //    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
     //    //curl_setopt($curl, CURLOPT_REFERER, $website);
     //    curl_setopt($curl, CURLOPT_POST, 1);
     //    curl_setopt($curl, CURLOPT_PORT,80);
     //    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($request_data, '', '&'));
     //    curl_setopt($curl, CURLOPT_TIMEOUT, 90);
     //    curl_setopt($curl, CURLOPT_HEADER, 0);
     //    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

     //    $result = curl_exec($curl);
     //    curl_close($curl);
     //    $xml = simplexml_load_string($result);
     //    $json = json_encode($xml);
     //    $array = json_decode($json,TRUE);
        $orderNo = time();
    	$hash_data = '60024'.'60024001'.$orderNo.'USD'.'100'.'5111111111111118'.'2024'.'12'.'123'.'6xb0d8bl';

        $signature = hash('sha256', trim($hash_data));
    	$request_data = [
            'merNo' => '60024',
            'terminalNo' => '60024001',
            'orderNo' => $orderNo,
            'orderCurrency' => 'USD',
            'orderAmount' => '100',
            'cardNo' => '5111111111111118',
            'cardExpireMonth' => '12',
            'cardExpireYear' => '2024',
            'cardSecurityCode' => '123',
            'firstName' => 'test',
            'lastName' => 'last',
            'email' => 'test12@yopmail.com',
            'ip' => '52.56.249.139',
            'phone' => '9876540987',
            'country' => 'US',
            'state' => 'test',
            'city' => 'test',
            'address' => 'address',
            'zip' => '382481',
            'encryption' => $signature,
            'webSite' => 'https://casino360.bet/en',
            'uniqueId' => (string) \Str::uuid(),
        ];
      // echo "<pre>";print_r($request_data);exit();
     //    echo $signature;exit();
        $request_query = http_build_query($request_data);

        $gateway_url = 'https://secure.dixonpay.com/test/payment';
        
        $curl = curl_init(); 
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_URL, $gateway_url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request_query);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        $result = curl_exec($curl);
        echo "<pre>";print_r($result);exit();
        curl_close($curl);
    }

    public function fillTestTransactions(Request $request)
    {
    	for ($i=0; $i < 10 ; $i++) { 
	    	$ran = ['0', '1', '5'];
	        $status = $ran[array_rand($ran, 1)];

	        $users = ['18'];
	        $user_id = $users[array_rand($users, 1)];

	        $currency = ['USD', 'GBP', 'EUR', 'CAD'];
	        $currency_id = $currency[array_rand($currency, 1)];

	        $randomDate = $this->randomDate('2021-05-01', '2021-07-04');
	        // dd($randomDate);

    		$input["first_name"] = \Str::random(10);
			$input["last_name"] = \Str::random(10);
			$input["address"] = \Str::random(20);
			$input["customer_order_id"] = "";
			$input["country"] = "IN";
			$input["state"] =\Str::random(10);
			$input["city"] = \Str::random(10);
			$input["zip"] ="380015";
			$input["email"] = \Str::random(10).'@gmail.com';
			$input["phone_no"] = rand(9111111111, 9999999999);
			$input["card_type"] = rand(2, 3);
			$input["amount"] = rand(10,2000);
			$input["currency"] = $currency_id;
			$input["order_id"] = time().strtoupper(\Str::random(10));
			$input["session_id"] = time().strtoupper(\Str::random(10));
			$input["user_id"] = $user_id;
			$input["status"] = $status;
			$input["reason"] = ($input['status'] == '1')?'Approved':'Your transaction was declined.';
			$input["gateway_id"] = '1';
			$input["payment_gateway_id"] = rand(1,2);
			$input["transaction_date"] = $randomDate;
			$input["created_at"] = $randomDate;
			$input["updated_at"] = $randomDate;
			// $input["transaction_date"] = date("Y-m-d H:i:s");
			// $input["created_at"] = date("Y-m-d H:i:s");
			// $input["updated_at"] = date("Y-m-d H:i:s");
			$input["card_no"] = rand(1111111111111111, 9999999999999999);
			//$input["transaction_date"] = date("Y-m-d",strtotime('tomorrow'));
			DB::table("transactions")->insert($input);
    	}
    }

    public function randomDate($start_date, $end_date)
	{
	    // Convert to timetamps
	    $min = strtotime($start_date);
	    $max = strtotime($end_date);

	    // Generate random number using above bounds
	    $val = rand($min, $max);

	    // Convert back to desired date format
	    return date('Y-m-d H:i:s', $val);
	}

    public function testwyre(Request $request) {
        $mainURL = 'https://api.testwyre.com/v3/';
        $CreateOrder = $mainURL.'orders/reserve';
        $headers = [
            'Authorization: Bearer SK-TLZYN3YY-8QVQXV2G-WRRBV3BD-WN33RPW2',
            'Content-Type: application/json',
        ];
        $requestData = $request->all();
        if(isset($requestData['smsNeeded']) && !empty($requestData['smsNeeded'])) {
            $Order_authorize = $mainURL.'debitcard/authorize/partner';

            $smsNeeded = $requestData['smsNeeded'];
            $reservation = $requestData['reservation'];
            $OrderID = $requestData['OrderID'];
            $data = [
                        "type" => "SMS",
                        "walletOrderId" => $OrderID,
                        "reservation" => $reservation,
                        "sms" => $smsNeeded,
                        "card2fa" => $smsNeeded
                    ];
            $data = json_encode($data);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $Order_authorize);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response_body = curl_exec($ch);
            curl_close ($ch);
            $response_body = json_decode($response_body, true);
            prd($response_body);
            exit;
        }
        $amount = 10;
        $referrerAccountId = "AC_8LR8ZUNUV3W";
        $data = [
                "amount" => $amount,
                "sourceCurrency" => "USD",
                "referrerAccountId" => $referrerAccountId ];
        $data = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $CreateOrder);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_body = curl_exec($ch);
        curl_close ($ch);
        $response_body = json_decode($response_body, true);

        if(isset($response_body['reservation']) && !empty($response_body['reservation'])) {

            $CreatePartner = $mainURL.'debitcard/process/partner';

            $reservation = $response_body['reservation'];
            $data = [
                "debitCard" => [
                  "number" => "4111111111111111",
                  "year" => "2023",
                  "month" => "01",
                  "cvv" => "123"
                ],
                "address" => [
                  "street1" => "asdfst Ave",
                  "city" => "asdfasdf",
                  "state" => "VUJ",
                  "country"  => "IN",
                  "postalCode" => "382210"
                ],
                "reservationId" => $reservation,
                "amount" => $amount,
                "sourceCurrency" => "USD",
                "destCurrency" => "BTC",
                "dest" => "bitcoin:tb1q6yn0ajs733xsk25vefrhwjey4629qt9c67y6ma",
                "referrerAccountId" => $referrerAccountId,
                "givenName" => "Crash",
                "familyName" => "Bandicoot",
                "email" => "test@sendwyre.com",
                "phone" => 1234567890,
                "referenceId" => $referrerAccountId,
                "ipAddress" => "1.1.1.1"
            ];
            $data = json_encode($data);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $CreatePartner);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response_body = curl_exec($ch);
            curl_close ($ch);
            $response_body = json_decode($response_body, true);
            if(isset($response_body['id'])) {
                $OrderID = $response_body['id'];
                // echo '<pre>';print_r($response_body);
                sleep(8);
                $CheckOrder = $mainURL.'debitcard/authorization/'.$OrderID;
                // echo "<br>".$CheckOrder;
                // echo "<br>";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $CheckOrder);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                $response_body = curl_exec($ch);
                curl_close ($ch);
                $response_body = json_decode($response_body, true);
                if(isset($response_body['smsNeeded']) && $response_body['smsNeeded'] == 1) {
                    return view('test.testwyre', compact('reservation', 'OrderID'));
                } else {
                    exit("SMS return false");
                }
            }
        }
        exit("Some Wrong");
    }

    public function testReport()
    {
        $payment_gateway_id = ['1','2'];
        
        $currencyArray = DB::table("transactions as t")
            ->select("currency")
            ->whereNotIn('t.payment_gateway_id', $payment_gateway_id)
            ->where('created_at', '<=', date('2021-11-30 23:59:59'))
            ->where('created_at', '>=', date('2021-07-26 00:00:00'))
            ->where('t.deleted_at', NULL)
            ->groupBy("t.currency")
            ->pluck("currency")->toArray();
        
        $transaction = [];
        foreach ($currencyArray as $key => $value) {
            
            $transaction[$value] = DB::table("transactions as t")
            ->selectRaw("
                sum(if(t.status = '1', amount, 0.00)) as successfullV,
                sum(if(t.status = '1', 1, 0)) as successfullC,
                sum(if(t.status = '0' , amount,0.00 )) as declinedV,
                sum(if(t.status = '0', 1, 0)) as declinedC,
                sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', amount, 0)) as chargebackV,
                sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', 1, 0)) as chargebackC,
                sum(if(t.status = '1' and t.is_retrieval = '1' and t.is_retrieval_remove= '0', amount, 0)) as retrievalV,
                sum(if(t.status = '1' and t.is_retrieval = '1' and t.is_retrieval_remove= '0', 1, 0)) as retrievalC,
                sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', amount, 0)) as refundV,
                sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', 1, 0)) as refundC"
            )
            ->whereNotIn('t.payment_gateway_id', $payment_gateway_id)
            ->where('created_at', '<=', date('2021-11-30 23:59:59'))
            ->where('created_at', '>=', date('2021-07-26 00:00:00'))
            ->where('t.deleted_at', NULL)
            ->where('t.currency', $value)->first();
        }
        
        view()->share('transactions', $transaction);
        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('sample_report_PDF'));
        
        $dompdf->setPaper([0, 0, 800.98, 700.85], 'landscape');
        $dompdf->render();
        $dompdf->stream('report.pdf');
    }
}
