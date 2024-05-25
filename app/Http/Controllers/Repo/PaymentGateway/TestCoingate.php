<?php

namespace App\Http\Controllers\Repo\PaymentGateway;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\StoreTransaction;

class TestCoingate extends Controller {

    use StoreTransaction;

    public function checkout($input, $check_assign_mid) {
        $url = 'https://api-sandbox.coingate.com/v2/orders';
        $apiKey = $check_assign_mid->auth_token;
        $data = [
            "order_id" => $input['order_id'],
            "title" => $input['first_name']. ' '. $input['last_name'],
            "purchaser_email" => $input['email'],
            "price_amount" => $input['converted_amount'],
            "price_currency"=> $input['converted_currency'],
            "receive_currency"  => 'DO_NOT_CONVERT',
            "callback_url" => route('coingate-callbackUrl',$input['session_id']),
            "success_url" => route('coingate-successUrl',$input['session_id']),
            "cancel_url" => route('coingate-cancelUrl',$input['session_id']),
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Token '.$apiKey,
            )
        );
        $response = curl_exec($curl);
        curl_close($curl);
        $responseData = json_decode($response, true);
        try {
        	$responseData = json_decode($response, true);
	        if (isset($responseData['id']) && $responseData['id'] != null) {
	            $input['gateway_id'] = $responseData['id'];
	            if(isset($input['card_no'])) {
	                $input['card_no'] = substr($input['card_no'], 0,6).'XXXXXX'.substr($input['card_no'], -4);
	            } else {
	                $input['card_no'] = null;
	            }
	            $this->updateGatewayResponseData($input, $responseData);
	            return [
	                'status' => '7',
	                'reason' => '3DS link generated successfully, please redirect.',
	                'redirect_3ds_url' => $responseData['payment_url'],
	            ];
	        }
        } catch (Exception $e) {
        	\Log::info([
                'coingate_response_catch' => $e->getMessage()
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
            'coingate_success_response' => $request->all(),
            'session_id' =>$session_id
        ]);
        $input = json_decode($input_json['request_data'], true);
        $input['status'] = '1';
        $input['reason'] = 'Your transaction was proccessed successfully.';
        $input['response_url'] = route('hosted-checkout-response');
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
            'coingate_callback_response' => $request->all(),
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

    public function cancelUrl(Request $request, $session_id) {
        $input_json = TransactionSession::where('transaction_id', $session_id)
                                        ->orderBy('id', 'desc')
                                        ->first();
        if ($input_json == null) {
            return abort(404);
        }
        \Log::info([
            'coingate_cancel_response' => $request->all(),
            'session_id' =>$session_id
        ]);
        $input = json_decode($input_json['request_data'], true);
        $input['status'] = '0';
        $input['reason'] = 'Your transaction was canceled successfully.';
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }
}
