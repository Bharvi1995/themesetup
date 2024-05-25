<?php

namespace App\Http\Controllers\Repo\PaymentGateway;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\TransactionSession;

class Cryptoxa extends Controller
{
    use StoreTransaction;
    
    const BASE_URL = 'https://app.cryptoxa.com';
    //const BASE_URL = 'https://qa.cryptoxa.com'; // test
    
    public function checkout($input, $check_assign_mid)
    {
        $token = $check_assign_mid->token;
        $data = [
            'type' => 'sell', // or buy
            'crypto' => 'BTC',
            'currency' => $input['currency'],
            'currency_amount' => $input['converted_amount'],
            // 'wallet' => '', // e.g. 3JA2mTL4ZLkLpRCSo234nD3QhQJQeGAgDM - only requried when type is buy
            'meta' => [
                'payer' => [
                    'email' => $input['email']
                ]
            ],
            'custom' => [
                'callback_url' => 'https://webhook.site/87bff569-b33e-4587-80b9-3180cbdc4475',
                'redirect_url' => 'https://webhook.site/87bff569-b33e-4587-80b9-3180cbdc4475',
                //'callback_url' => route('cryptoxa-callback',$input['session_id']),
                //'redirect_url' => route('cryptoxa-redirect',$input['session_id']),
                // 'fee' => 2.5, // if no value means default fee will be deduct
                'order_id' => $input['order_id']
            ],
        ];
        $arr = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::BASE_URL . '/api/transactions');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER,[
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ]);
        $response = curl_exec($curl);
        
        curl_close($curl);
        $responseData = json_decode($response, 1);
        
        \Log::info([
            'cryptoxa-input' => $data
        ]);
        
        \Log::info([
            'cryptoxa-response' => $responseData
        ]);
        // echo "<pre>";print_r($response);exit();
        $input['gateway_id'] = $responseData['transaction']['id'] ?? null;
        $this->updateGatewayResponseData($input, $responseData);
        // transaction requires 3DS redirect
        if(! empty($responseData['transaction']['pay_link'])) {
            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                'redirect_3ds_url' => $responseData['transaction']['pay_link'],
            ];
        } else if (! empty($responseData['transaction']['id'])) {
            return [
                'status' => '1',
                'reason' => 'Your transaction has been processed successfully.',
                'order_id' => $input['order_id'],
            ];
        }
        return [
            'status' => '0',
            'reason' => 'Your transaction could not processed.',
            'order_id' => $input['order_id'],
        ];
    }
    
    public function callback(Request $request,$id) {
        $input_json = TransactionSession::where('transaction_id', $id)
            ->orderBy('id', 'desc')
            ->first();
        if ($input_json == null) {
            return abort(404);
        }
        $body = json_decode(request()->getContent(), true);
        \Log::info([
            'cryptoxa-webhook' => $body
        ]);
        $input = json_decode($input_json['request_data'], true);
        if (! empty($body['custom']['order_id'])) {
            $input['status'] = '2';
            $input['reason'] = 'Your transaction is in Pending.';
            if (! empty($body['status']) && $body['status'] == 'Paid') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccessed successfully.';
            }else{
                $input['status'] = '0';
                $input['reason'] = 'Your transaction was Declined.';
            }
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        }
    }

    public function redirect($session_id){
        $input_json = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first();

        if ($input_json == null) {
            exit();
        }
        $input = json_decode($input_json->request_data, true);
        $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$check_assign_mid->token
        ];
        $url = self::BASE_URL . '/api/transactions/'.$input_json->gateway_id.'/show';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_body = curl_exec($ch);
        curl_close ($ch);
        $response_body = json_decode($response_body);
        if($response_body->transaction->status == "Paid"){
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';
        }else{
            $input['status'] = '0';
            $input['reason'] = 'Your transaction was Declined.';
        }
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }
}