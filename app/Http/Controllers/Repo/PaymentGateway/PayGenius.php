<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\User;
use App\Transaction;
use App\TransactionSession;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PaymentResponse;

class PayGenius extends Controller
{
	use StoreTransaction;

	const BASE_URL = 'https://www.paygenius.co.za';

	// ================================================
    /* method : __construct
    * @param  :
    * @Description : Create a new controller instance.
    */// ==============================================
    public function __construct()
    {
        $this->transaction = new Transaction;
        $this->transactionSession = new TransactionSession;
    }

    // ================================================
    /* method : checkout
    * @param  : 
    * @description : initialize transaction request
    */// ===============================================
    public function checkout($input, $check_assign_mid)
    {
    	$validate_url = self::BASE_URL.'/pg/api/v2/util/validate';

    	$validate_array = $this->createPayload($input, $check_assign_mid);

    	$validate_data = json_encode($validate_array);
		$secret = $check_assign_mid->secret_key;

		$signature = hash_hmac('sha256', trim($validate_url."\n".$validate_data), $secret);

		$validate_headers = [
			'Accept: application/json',
            'Content-Type: application/json',
            'X-Signature: '.$signature,
            'X-Token: '.$check_assign_mid->access_token
        ];

    	// validate
    	$validate_response = $this->curlPost($validate_url, $validate_data, $validate_headers);

    	if (isset($validate_response['success']) && $validate_response['success'] != true) {
    		$return_data['status'] = '0';
            $return_data['reason'] = 'Transaction couldn\'t be verified.';

            return $return_data;
    	}

    	$payment_url = self::BASE_URL.'/pg/api/v2/payment/create';

    	$payment_array = $this->createPayload($input, $check_assign_mid);

    	$payment_data = json_encode($payment_array);
		$secret = $check_assign_mid->secret_key;

		$signature = hash_hmac('sha256', trim($payment_url."\n".$payment_data), $secret);

		$payment_headers = [
			'Accept: application/json',
            'Content-Type: application/json',
            'X-Signature: '.$signature,
            'X-Token: '.$check_assign_mid->access_token
        ];

    	// create payment
    	$create_payment = $this->curlPost($payment_url, $payment_data, $payment_headers);

        $input['gateway_id'] = $create_payment['reference'] ?? null;

    	TransactionSession::where('transaction_id', $input['session_id'])
    		->update([
                'request_data' => json_encode($input),
                'response_data' => json_encode($create_payment)
            ]);

    	if (isset($create_payment['success']) && $create_payment['success'] != true) {
    		$return_data['status'] = '0';
            $return_data['reason'] = $create_payment['message'] ?? 'Couldn\'t create 3DS link.';
            $return_data["order_id"] = $input["order_id"];
            return $return_data;
    	}

    	return [
            'status' => '7',
            'reason' => '3DS link generated successfully, please redirect.',
            'redirect_3ds_url' => route('payGenius.confirmPayment', $input['session_id'])
        ];
    }

    // ================================================
    /* method : confirmPayment
    * @param  : 
    * @description : redirect for confirm 3ds payment request
    */// ===============================================
    public function confirmPayment($session_id)
    {
    	$input_json = TransactionSession::where('transaction_id', $session_id)
            ->where('is_completed', 0)
            ->orderBy('id', 'desc')
            ->first();
        
        if (empty($input_json)) {
            return abort(404);
        }

        $input = json_decode($input_json['request_data'], true);
        $response_data = json_decode($input_json['response_data'], true);

        if (empty($response_data)) {
            return abort(404);
        }

        return view('gateway.paygenius', compact('session_id', 'response_data'));
    }

    // ================================================
     /* method : createPayload
     * @param  : 
     * @description : payload to create payment
     */// ===============================================
     public function createPayload($input)
    {
    	$payment_array = [
    		'creditCard' => [
    			'number' => $input['card_no'],
    			'cardHolder' => $input['first_name']. ' '.$input['last_name'],
    			'expiryYear' => (int)$input['ccExpiryYear'],
    			'expiryMonth' => (int)$input['ccExpiryMonth'],
    			'type' => config('card.type.'.$input['card_type']),
    			'cvv' => $input['cvvNumber'],
    		],
    		'transaction' => [
    			'reference' => $input['session_id'],
    			'currency' => $input["converted_currency"],
    			'amount' => floatval($input['converted_amount'] * 100),
    		],
    		'threeDSecure' => true
    	];

    	return $payment_array;
    }

    // ================================================
    /* method : redirect
    * @param  : 
    * @description : redirect after 3ds
    */// ===============================================
    public function redirect(Request $request, $session_id)
    {
    	$request_data = $request->only(['PaRes', 'MD']);

    	$input_json = TransactionSession::where('transaction_id', $session_id)
            ->where('is_completed', 0)
            ->orderBy('id', 'desc')
            ->first();
        
        if (empty($input_json)) {
            return abort(404);
        }

        $input = json_decode($input_json['request_data'], true);
        $response_data = json_decode($input_json['response_data'], true);

        if (empty($response_data)) {
            return abort(404);
        }

        if (empty($request_data['PaRes'])) {
            return abort(404);
        }

        $confirm_url = self::BASE_URL.'/pg/api/v2/payment/'.$response_data['reference'].'/confirm';

        $confirm_array = [
        	'paRes' => $request_data['PaRes']
        ];

        $confirm_data = json_encode($confirm_array);

        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

		$secret = $check_assign_mid->secret_key;

		$signature = hash_hmac('sha256', trim($confirm_url."\n".$confirm_data), $secret);

		$confirm_headers = [
			'Accept: application/json',
            'Content-Type: application/json',
            'X-Signature: '.$signature,
            'X-Token: '.$check_assign_mid->access_token
        ];

    	// confirm payment
    	$confirm_payment = $this->curlPost($confirm_url, $confirm_data, $confirm_headers);

        // confirm success
        if (isset($confirm_payment['success']) && $confirm_payment['success'] == true) {

            // execute payment
            $execute_url = self::BASE_URL.'/pg/api/v2/payment/'.$response_data['reference'].'/execute';

            $execute_array = [
                'transaction' => [
                    'currency' => $input['converted_currency'],
                    'amount' => floatval($input['converted_amount'] * 100)
                ]
            ];

            $execute_data = json_encode($execute_array);

            $exec_signature = hash_hmac('sha256', trim($execute_url."\n".$execute_data), $secret);

            $execute_headers = [
                'Accept: application/json',
                'Content-Type: application/json',
                'X-Signature: '.$exec_signature,
                'X-Token: '.$check_assign_mid->access_token
            ];

            // create payment
            $execute_payment = $this->curlPost($execute_url, $execute_data, $execute_headers);

            // transaction success
            if (isset($execute_payment['success']) && $execute_payment['success'] == true) {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccess successfully.';
            } else {
                \Log::info(['execute' => $execute_payment]);
                $input['status'] = '0';
                $input['reason'] = $execute_payment['message'] ?? 'Your transaction was declined due to authorization.';
            }
            
        } else {
            \Log::info(['confirm' => $confirm_payment]);
            $input['status'] = '0';
            $input['reason'] = $confirm_payment['message'] ?? 'Your transaction was declined due to authorization';
        }

    	// redirect back to $response_url
        $transaction_response = $this->storeTransaction($input);
        
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    // ================================================
    /* method : notify
    * @param  : 
    * @description : notify after 3ds
    */// ===============================================
    public function notify(Request $request, $session_id)
    {
    	\Log::info($request->all());
    }

    // ================================================
    /* method : curlPost
    * @param  : 
    * @description : post curl request
    */// ===============================================
    public function curlPost($url, $payload, $headers = null)
    {
    	$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($headers != null) {
        	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response_body = curl_exec($ch);

        curl_close ($ch);

        return json_decode($response_body, 1);
    }
}