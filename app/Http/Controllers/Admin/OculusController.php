<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MIDDetail;
use Carbon\Carbon;


class OculusController extends Controller
{
    public function refund()
    {
        return view('gateway.oculus.refund');
    }

    public function store(Request $request)
    {
        $request_url = "https://prod.mycardstorage.com/api/api.asmx";
        $transactionData = \DB::table("transactions")->where("order_id",$request->order_id)->first();
        if ($transactionData == null) {
            notificationMsg('error', "Transaction Details Not Found");
            return redirect()->route('oculus-refund');
        }
        $check_assign_mid = checkAssignMid($transactionData->payment_gateway_id);
        
        $msc_account_id = $check_assign_mid->msc_account_id;
        $apiKey = $check_assign_mid->api_key;

        $MCSTransactionID = $transactionData->gateway_id;
        $converted_amount = $transactionData->converted_amount;
        \Log::info([
            'oculus-refund-request' => array(
                'msc_account_id' => $msc_account_id,
                'apiKey' => $apiKey,
                'MCSTransactionID' => $MCSTransactionID,
                'converted_amount' => $converted_amount,
            ),
        ]);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $request_url,
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
                  <!--REQUIRED:-->
                 <myc:ApiKey>'.$apiKey.'</myc:ApiKey>
               </myc:AuthHeader>
            </soapenv:Header>
            <soapenv:Body>
               <myc:CreditCredit_Soap>
                  <!--Optional:-->
                  <myc:creditCardCredit>
                        <!--REQUIRED:-->
                     <myc:ServiceSecurity>
                        <myc:MCSAccountID>'.$msc_account_id.'</myc:MCSAccountID>
                     </myc:ServiceSecurity>
                     <myc:TokenData>
                     <!--
                        <myc:CardHolderID></myc:CardHolderID>
                        <myc:CardHolderName></myc:CardHolderName>
                        <myc:CardNumber></myc:CardNumber>
                        <myc:ExpirationMonth></myc:ExpirationMonth>
                        <myc:ExpirationYear></myc:ExpirationYear>
                        <myc:CVV></myc:CVV>
         
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
                        <myc:Amount>'.$converted_amount.'</myc:Amount>
                        <myc:TicketNumber>'.$transactionData->session_id.'</myc:TicketNumber>
                        <myc:MCSTransactionID>'.$request->gateway_order_id_oculus.'</myc:MCSTransactionID>
                        <!--Optional - Use these for reporting & tracking :-->
                        <myc:ReferenceNumber></myc:ReferenceNumber>
                        
                        <myc:Custom1></myc:Custom1>
                        <myc:Custom2></myc:Custom2>
                        <myc:Custom3></myc:Custom3>
                        <myc:Custom4></myc:Custom4>
                        <myc:Custom5></myc:Custom5>
                        <myc:Custom6></myc:Custom6>
                     </myc:TransactionData>
                  </myc:creditCardCredit>
               </myc:CreditCredit_Soap>
            </soapenv:Body>
         </soapenv:Envelope>',
            CURLOPT_HTTPHEADER => array(
                    'Content-Type: text/xml; charset=utf-8',
                    'Cache-Control: no-cache'
                ),
        ));

        $response = curl_exec($curl);
        $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", '$1$2$3', $response);
        $xml = simplexml_load_string($xml);
        $json = json_encode($xml);
        $response_data = json_decode($json,true);
        \Log::info([
            'oculus-refund-response' => $response_data,
        ]);
        curl_close($curl);
        
        if(isset($response_data['soapBody']['CreditCredit_SoapResponse']['CreditCredit_SoapResult']['Result']['ResultCode']) && $response_data['soapBody']['CreditCredit_SoapResponse']['CreditCredit_SoapResult']['Result']['ResultCode'] == '0') {
            
            notificationMsg('success', "Refund has request for order number ".$request->order_id." has been initiated");
        } else {
            notificationMsg('error', isset($response_data['soapBody']['CreditCredit_SoapResponse']['CreditCredit_SoapResult']['Result']['ResultDetail']) ?$response_data['soapBody']['CreditCredit_SoapResponse']['CreditCredit_SoapResult']['Result']['ResultDetail']:"Something went wrong .. !!");
        }
        return redirect()->route('oculus-refund');
    }
}
