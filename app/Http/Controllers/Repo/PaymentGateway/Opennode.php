<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use URL;
use View;
use Input;
use Session;
use Redirect;
use Validator;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\StoreTransaction;

class Opennode extends Controller {

    use StoreTransaction;

    public function checkout($input, $check_assign_mid)
    {
        $url = 'https://api.opennode.com/v1/charges';
        $apiKey = $check_assign_mid->api_key;
        $data = [
            "description" => '',
            "amount" => $input['converted_amount'],
            "currency"=> $input['converted_currency'],
            "customer_email" => $input['email'],
            "customer_name" => $input['first_name']. ' '. $input['last_name'],
            "order_id" => $input['order_id'],
            "callback_url" => route('opennode-callbackUrl',$input['session_id']),
            "success_url" => route('opennode-successUrl',$input['session_id']),
            "auto_settle" => true
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Authorization: '.$apiKey,
            )
        );
        $response = curl_exec($curl);
        curl_close($curl);
        try {
        	$responseData = json_decode($response, true);
	        if (isset($responseData['data']['id']) && $responseData['data']['id'] != null) {
	            $input['gateway_id'] = $responseData['data']['id'];
	            if(isset($input['card_no'])) {
	                $input['card_no'] = substr($input['card_no'], 0,6).'XXXXXX'.substr($input['card_no'], -4);
	            } else {
	                $input['card_no'] = null;
	            }
	            $this->updateGatewayResponseData($input, $responseData);
	            return [
	                'status' => '7',
	                'reason' => '3DS link generated successfully, please redirect.',
	                'redirect_3ds_url' => 'https://checkout.opennode.com/'.$responseData['data']['id'],
	            ];
	        }
        } catch (Exception $e) {
        	\Log::info([
                'opennode_response_catch' => $e->getMessage()
            ]);
        }
        $input['status'] = '0';
        $input['reason'] = 'Your transaction was declined by issuing bank.';
        return [
            'status' => '0',
            'reason' => $input['reason'],
            'order_id' => $input['order_id'],
        ];
    }

    public function successUrl(Request $request, $session_id)
    {
    	$input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();
        if ($input_json == null) {
            return abort(404);
        }
        \Log::info([
            'opennode_success_response' => $request->all(),
            'session_id' =>$session_id
        ]);
        $input = json_decode($input_json['request_data'], true);
        if($request->status == "paid") {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';
        }else {
            $input['status'] = '2';
            $input['reason'] = 'Your transaction is in progress, it will be completed within 24 hours.';
        }
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function callbackUrl(Request $request, $session_id) {
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();
        if ($input_json == null) {
            return abort(404);
        }
        \Log::info([
            'opennode_callback_response' => $request->all(),
            'session_id' =>$session_id
        ]);
        $input = json_decode($input_json['request_data'], true);
        if($input_json->created_at < date('Y-m-d H:i:s', strtotime('-2 hour'))) {
            $input['status'] = '0';
            $input['reason'] = 'transaction expired.';
        } else {
            if($request->status == "paid") {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccessed successfully.';
            } else {
                $input['status'] = '2';
                $input['reason'] = 'Your transaction is in progress, it will be completed within 24 hours.';
            }
        }
        $transaction_response = $this->storeTransaction($input);
    }

}
