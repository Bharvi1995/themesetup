<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Mail;
use Session;
use Exception;
use App\User;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use App\TransactionSession;
use App\Transaction;
use Carbon\Carbon;

class Oculus extends Controller
{
    use StoreTransaction;

    // const BASE_URL = 'https://test.mycardstorage.com/api/api.asmx'; // test
    const BASE_URL = 'https://prod.mycardstorage.com/api/api.asmx'; // Live

    public function __construct()
    {
        $this->transaction = new Transaction;
        $this->transactionSession = new TransactionSession;
    }
    
    public function checkout($input, $check_assign_mid)
    {
        // dd($input);
        try {
            $request_url = self::BASE_URL;
            $api_key = $check_assign_mid->api_key;
            $msc_account_id = $check_assign_mid->msc_account_id;
            
            

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
                    <myc:ApiKey>'.$api_key.'</myc:ApiKey>
                </myc:AuthHeader>
            </soapenv:Header>
            <soapenv:Body>
                <myc:CreditSale_Soap>
                    <myc:creditCardSale>
                        <myc:ServiceSecurity>
                        <myc:MCSAccountID>'.$msc_account_id.'</myc:MCSAccountID>
                        </myc:ServiceSecurity>
                        <myc:TokenData>
                        <myc:CardHolderName>'.$input['first_name'].' '.$input['last_name'].'</myc:CardHolderName>
                        <myc:CardNumber>'.$input['card_no'].'</myc:CardNumber>
                        <myc:CVV>'.$input['cvvNumber'].'</myc:CVV>
                        <myc:ExpirationMonth>'.$input['ccExpiryMonth'].'</myc:ExpirationMonth>
                        <myc:ExpirationYear>'.substr($input['ccExpiryYear'], -2).'</myc:ExpirationYear>
                        <myc:StreetAddress>'.$input['address'].'</myc:StreetAddress>
                        <myc:City>'.$input['city'].'</myc:City>
                        <myc:State>'.$input['state'].'</myc:State>
                        <myc:ZipCode>'.$input['zip'].'</myc:ZipCode>
                        <myc:Country>'.$input['country'].'</myc:Country>
                        <myc:EmailAddress>'.$input['email'].'</myc:EmailAddress>
                        <myc:Phone>'.$input['phone_no'].'</myc:Phone>
                        </myc:TokenData>
                        <myc:TransactionData>
                        <myc:Amount>'.$input['converted_amount'].'</myc:Amount>
                        <myc:CurrencyCode>840</myc:CurrencyCode>
                        <myc:TicketNumber>'.$input['session_id'].'</myc:TicketNumber>
                        </myc:TransactionData>
                    </myc:creditCardSale>
                </myc:CreditSale_Soap>
            </soapenv:Body>
            </soapenv:Envelope>',
            CURLOPT_HTTPHEADER => array(
                    'Content-Type: text/xml; charset=utf-8',
                    'Cache-Control: no-cache'
                ),
            ));

            $response = curl_exec($curl);

            // \Log::info([
            //     'Oculus-payment-curl-xml-response' => $response
            // ]);

            $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", '$1$2$3', $response);
            $xml = simplexml_load_string($xml);
            $json = json_encode($xml);
            $response_data = json_decode($json,true);

            // \Log::info([
            //     'Oculus-payment-curl-array-response' => $response_data
            // ]);
            
            if(isset($response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['MCSTransactionID'])) {
                $input['gateway_id'] = $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['MCSTransactionID'] ?? null;

                $this->updateGatewayResponseData($input, $response_data);
            }

            if(isset($response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode']) && $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode'] == 0)
            {
                // status successful
                return [
                    'status' => '1',
                    'reason' => 'Your transaction has been processed successfully.',
                    'order_id' => $input['order_id'],
                ];
            } else if(isset($response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode']) && $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode'] == 4){
                $newArr = array(
                    'MCSTransactionID' => $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['MCSTransactionID'],
                    'ChallengeURL' => $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['AuthChallenge']['ChallengeURL'],
                    'ChallengeKey' => $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['AuthChallenge']['ChallengeKey'],
                    'XID' => isset($response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['AuthChallenge']['XID']) ? $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['AuthChallenge']['XID'] : '',
                    'CAVV' => $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['AuthChallenge']['CAVV'],
                    'CompleteChallengeURL' => isset($response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['AuthChallenge']['CompleteChallengeURL'])?$response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['AuthChallenge']['CompleteChallengeURL']:null,
                );
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                    'redirect_3ds_url' =>  route('oculus.input', $newArr),
                ];    
            } else if(isset($response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode']) && $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode'] == 1){

                return [
                    'status' => '0',
                    'reason' => $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultDetail'] ?? 'Transaction authentication failed.',
                    'order_id' => $input['order_id'],
                ];
            } else if(isset($response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode']) && $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultCode'] == 2){

                return [
                    'status' => '0',
                    'reason' => $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultDetail'] ?? 'Transaction authentication failed.',
                    'order_id' => $input['order_id'],
                ];
            } else {
                return [
                    'status' => '0',
                    'reason' => $response_data['soapBody']['CreditSale_SoapResponse']['CreditSale_SoapResult']['Result']['ResultDetail'] ?? 'Transaction authentication failed.',
                    'order_id' => $input['order_id'],
                ];
            }

        } catch (\Exception $e) {
            
            \Log::info([
                'Oculus-exception' => $e->getMessage()
            ]);

            return [
                'status' => '0',
                'reason' => $e->getMessage(), // 'Your transaction could not processed.',
                'order_id' => $input['order_id']
            ];
        }
    }

    public function inputSubmit(Request $request)
    {
        $input = $request->all();
        return view('gateway.oculus.input', compact('input'));
    }

    public function callBack(Request $request)
    {
        $body = $request->all();
        // \Log::info([
        //     'Oculus-callback-response' => $body
        // ]);

        if(!isset($body['referenceNumber']) || $body['referenceNumber'] == ""){
            return abort(404);
        }
        if(!isset($body['ticketNumber']) || $body['ticketNumber'] == ""){
            return abort(404);
        }
        $data = \DB::table('transaction_session')
                ->where('gateway_id', $body['referenceNumber'])
                ->where('transaction_id', $body['ticketNumber'])
                ->first();
        
        if ($data == null) {
            return abort(404);
        }

        $input = json_decode($data->request_data, 1);

        if (isset($body['result']) && $body['result'] == 'Approved') {
            
            // transaction Success 
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';

        } else if(isset($body['result']) && $body['result'] == 'Decline') {
            
            $input['status'] = '0';
            $input['reason'] = $body['resultDetail'] ?? 'Transaction authentication failed.';

        } else {
            $input['status'] = '0';
            $input['reason'] = $body['resultDetail'] ?? $body['status_message'] ?? 'Transaction authentication failed.';
        }
        
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function webhook(Request $request)
    {
        $body = $request->all();
        // \Log::info([
        //     'Oculus-webhook-response' => $body
        // ]);

        if(!isset($body['referenceNumber']) || $body['referenceNumber'] == ""){
            return abort(404);
        }
        if(!isset($body['ticketNumber']) || $body['ticketNumber'] == ""){
            return abort(404);
        }
        $data = \DB::table('transaction_session')
                ->where('gateway_id', $body['referenceNumber'])
                ->where('transaction_id', $body['ticketNumber'])
                ->first();
        
        if ($data == null) {
            return abort(404);
        }

        $input = json_decode($data->request_data, 1);

        if (isset($body['result']) && $body['result'] == 'Approved') {
            
            // transaction Success 
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';
            $this->storeTransaction($input);

            \Log::info(['type' => 'webhook', 'body' => $body['ticketNumber'].' confirm.']);
            exit();

        } else if(isset($body['result']) && $body['result'] == 'Decline') {
            
            $input['status'] = '0';
            $input['reason'] = $body['resultDetail'] ?? 'Transaction authentication failed.';
            $this->storeTransaction($input);

            \Log::info(['type' => 'webhook', 'body' => $body['ticketNumber'].' failed.']);
            exit();
        } else {
            
            $input['status'] = '0';
            $input['reason'] = $body['status_message'] ?? 'Transaction authentication failed.';
            $this->storeTransaction($input);

            \Log::info(['type' => 'webhook', 'body' => $body['ticketNumber'].' failed.']);
            exit();
        }
    }
}
