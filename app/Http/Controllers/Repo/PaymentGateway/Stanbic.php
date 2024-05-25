<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\Transaction;
use App\TransactionSession;
use Illuminate\Support\Facades\Hash;

class Stanbic extends Controller {

    //const BASE_URL = 'https://api-gateway.sandbox.ngenius-payments.com/'; // Test
    const BASE_URL = 'https://api-gateway.ngenius-payments.com/'; //Live

    use StoreTransaction;

    public function checkout($input, $check_assign_mid) {

        try {
            
            $token = $this->getAccessToken($input,$check_assign_mid);

            if (isset($token['access_token'])) {

                $input['token'] = $token['access_token'];
                $amount = ceil($input['converted_amount']);
                if($input["converted_currency"] == "USD"){
                    $amount = $input['converted_amount'] * 100;
                }
                $data = [
                    'action' => 'PURCHASE',
                    'amount' => [
                        'currencyCode' => $input["converted_currency"],
                        'value' => $amount
                    ],
                    "merchantAttributes" => [
                        "redirectUrl" => route('stanbic.callback',$input['session_id']),
                    ],
                    "emailAddress" => $input["email"]
                ];
                \Log::info([
                    'stanbic-data' => $data
                ]);
                $outlet = $check_assign_mid->reference;
                $json = json_encode($data); 
                $ch = curl_init();
                $url = self::BASE_URL . "transactions/outlets/".$outlet."/orders";
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    "Authorization: Bearer ".$input['token'], 
                    "Content-Type: application/vnd.ni-payment.v2+json",
                    "Accept: application/vnd.ni-payment.v2+json"
                ));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                $output = json_decode(curl_exec($ch)); 
                $response = json_decode(json_encode($output), true); 
                curl_close ($ch);
                $responseData = json_decode(json_encode($output), true);
                $link = $responseData['_links']['payment']['href'] ?? null;
                $input['gateway_id'] = $responseData["reference"] ?? null;
                $this->updateGatewayResponseData($input, $responseData);
                if( isset($link) && !empty($link) ) {
                    return [
                            'status' => '7',
                            'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                            'redirect_3ds_url' => $link,
                    ];
                }
                
            } 
            \Log::info([
                'stanbic-token-error' => $token
            ]);

            throw new \Exception($token['errors'][0]['errorCode'] ?? 'Your transaction could not processed.');
            
        } catch (\Exception $e) {

            \Log::info([
                'stanbic-exception' => $e->getMessage()
            ]);

            return [
                'status' => '0',
                'reason' => $e->getMessage(), // 'Your transaction could not processed.',
                'order_id' => $input['order_id']
            ];
        }
    }

    /*
     * For generate aceesst oken
     * */
    private function getAccessToken($input, $check_assign_mid) {

        $apikey = $check_assign_mid->api_key;  
        $data = [
            'realmName' => $check_assign_mid->realm_name
        ];
        $url = self::BASE_URL . 'identity/auth/access-token';
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "accept: application/vnd.ni-identity.v1+json",
            "authorization: Basic ".$apikey,
            "content-type: application/vnd.ni-identity.v1+json"
        )); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); 
        $output = json_decode(curl_exec($ch)); 
        $responseData = json_decode(json_encode($output), true);
        return $responseData;
    }

    public function callback(Request $request,$id)
    {
        $response = $request->all(); 
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $id)
            ->first();

        if ($data == null) {
            return abort(404);
        }
        $input = json_decode($data->request_data, 1);
        $orderno = $response['ref'] ?? null; 
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
        $responseData = $this->getOrderStatus($orderno,$input,$check_assign_mid);

        if($responseData['_embedded']['payment'][0]["state"] == "PURCHASED"){
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
        }else{
            $input['status'] = '0';
            $input['reason'] = isset($responseData['_embedded']['payment'][0]["3ds"]["summaryText"]) ? $responseData['_embedded']['payment'][0]["3ds"]["summaryText"] : 'Your transaction could not processed.';
        }
        // if($responseData['_embedded']['payment'][0]['3ds']['status'] == 'SUCCESS') {
        //     $input['status'] = '1';
        //     $input['reason'] = 'Your transaction has been processed successfully.';
        // } else {
        //     $input['status'] = '0';
        //     $input['reason'] = 'Your transaction could not processed.';
        // }
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input); 
        
        return redirect($store_transaction_link);
    }

    private function getOrderStatus($orderno, $input, $check_assign_mid)
    {
        $token = $this->getAccessToken($input,$check_assign_mid);
        
        if (isset($token['access_token'])) {

            $url = self::BASE_URL . "transactions/outlets/". $check_assign_mid->reference . "/orders/" . $orderno;
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer " . $token['access_token'], 
            )); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
            $output = json_decode(curl_exec($ch)); 
            $responseData = json_decode(json_encode($output), true);
            return $responseData;
        } else {
            return $token['errors'][0]['errorCode'];
        }
    }
}


