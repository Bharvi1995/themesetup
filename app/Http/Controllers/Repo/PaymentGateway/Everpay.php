<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\User;
use App\Transaction;
use App\TransactionSession;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Everpay extends Controller
{
	use StoreTransaction;

	const BASE_URL = 'https://platform.everpayinc.com';

	// ================================================
	/* method : __construct
	* @param  :
	* @description : create new instance of the class
	*/// ===============================================
	public function __construct()
	{
		$this->transaction = new Transaction;
	}

	// ================================================
	/* method : checkout
	* @param  :
	* @description : gateway main method
	*/// ===============================================
	public function checkout($input, $check_assign_mid)
	{
		$payment_url = self::BASE_URL.'/payment-invoices';

		$payment_data = [
			'data' => [
				'type' => 'payment-invoices',
				'attributes' => [
					'reference_id' => $input['session_id'],
					'description' => 'Payment order',
					'currency' => $input['converted_currency'],
					'amount' => $input['converted_amount'],
					'service' => 'payment_card_usd_hpp',
					// 'test_mode' => true, // set this true for test transaction
					'return_url' => 'https://testpay.com/everpay/return/'.$input['session_id'],
					'callback_url' => 'https://testpay.com/everpay/callback/'.$input['session_id'],
					"gateway_options" => array(
						"cardgate" => array(
							"tokenize" =>  true
						)
					),
					"customer" => array(
						"reference_id" => "my_customer_1"
					)
				],
			],
		];

		foreach ($payment_data as $k => $a) {
            $payment_data[$k] = json_decode(json_encode($a));
        }

		$payment_json = json_encode($payment_data);
		\Log::info(['everpay_request' => $payment_json]);

		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $payment_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payment_json);
        curl_setopt($ch, CURLOPT_USERPWD, $check_assign_mid->account_id.':'.$check_assign_mid->password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $payment_body = curl_exec($ch);

        curl_close ($ch);

        $payment_array =  json_decode($payment_body, 1);
		\Log::info(['everpay_response' => $payment_array]);

        // update session-data
        $input['gateway_id'] = $payment_array['data']['id'] ?? null;
    	$this->updateGatewayResponseData($input, $payment_array);

        if (
        	isset($payment_array['data']['attributes']['flow_data']['metadata']['token']) && !empty($payment_array['data']['attributes']['flow_data']['metadata']['token'])
        ) {
        	$card_url = 'https://cardgate.paycore.io/payment/sale';

        	$card_data = [
        		'data' => [
        			'type' => 'sale-operation',
        			'attributes' => [
        				'card_number' => $input['card_no'],
        				'card_holder' => $input['first_name'].' '.$input['last_name'],
        				'cvv' => $input['cvvNumber'],
        				'exp_month' => $input['ccExpiryMonth'],
        				'exp_year' => substr($input['ccExpiryYear'], -2),
        			],
        		],
        	];

        	$card_headers = [
        		'Authorization: Bearer '.$payment_array['data']['attributes']['flow_data']['metadata']['token'],
        	];

        	$card_json = json_encode($card_data);

			$ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $card_url);
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	        curl_setopt($ch, CURLOPT_HTTPHEADER, $card_headers);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $card_json);
	        // curl_setopt($ch, CURLOPT_USERPWD, $check_assign_mid->account_id.':'.$check_assign_mid->password);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	        $card_body = curl_exec($ch);

	        curl_close ($ch);

	        $card_array =  json_decode($card_body, 1);
	        \Log::info(['everpay_card_response' => $card_array]);

        	// update session-data
        	$this->updateGatewayResponseData($input, $card_array);

	        if (isset($card_array['status']) && $card_array['status'] == 'processed') {
	        	$input['status'] = '1';
	            $input['reason'] = 'Your transaction was processed successfully.';

	            return $input;
	        } elseif (isset($card_array['status']) && $card_array['status'] == 'process_pending') {

	        	return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect.',
                    'redirect_3ds_url' => route('everpay.form', $input['session_id']),
                ];

	        } elseif (isset($card_array['status']) && $card_array['status'] == 'process_failed') {
	        	$input['status'] = '0';
	            $input['reason'] = $card_array['errors'][0]['title'] ?? $card_array['errors'][0]['status'] ?? 'Processing has failed, may be set when an error occurs and the corresponding payment request has failed.';

	            return $input;

	        } else {
				$input['status'] = '0';
	            $input['reason'] = $card_array['errors'][0]['title'] ?? $card_array['errors'][0]['status'] ?? 'Transaction authorization failed.';

	            return $input;
	        }
        } elseif (isset($payment_array['error']['message']) && !empty($payment_array['error']['message'])) {
        	$input['status'] = '0';
            $input['reason'] = $payment_array['error']['message'] ?? $payment_array['errors'][0]['status'] ?? 'Transaction initialization failed.';

            return $input;
        } else {
        	$input['status'] = '0';
            $input['reason'] = $payment_array['errors'][0]['title'] ?? $payment_array['errors'][0]['status'] ?? 'Transaction initialization failed.';

            return $input;
        }
	}

	// ================================================
	/* method : form
	* @param  :
	* @description : loading 3ds form for submit
	*/// ===============================================
	public function form($session_id)
	{
		$transaction_session = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            // ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->where('is_completed', 0)
            ->first();

        if (empty($transaction_session)) {
            abort(404);
        }

        $input = json_decode($transaction_session->request_data, true);
        $response_data = json_decode($transaction_session->response_data, true);

        $action = $response_data['auth_payload']['action'];
        $method = $response_data['auth_payload']['method'];
        $fields = $response_data['auth_payload']['params'];

        $check_assign_mid = checkAssignMid($input['payment_gateway_id']);

        return view('gateway.everpay.form', compact('action', 'method', 'fields'));
	}

	// ================================================
	/* method : return
	* @param  :
	* @description : return back from everpay
	*/// ===============================================
	public function return(Request $request, $session_id)
	{
		$request_data = $request->all();

        $transaction_session = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            // ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->where('is_completed', 0)
            ->first();

        if (empty($transaction_session)) {
            abort(404);
        }

        $input = json_decode($transaction_session->request_data, true);

        $check_assign_mid = checkAssignMid($input['payment_gateway_id']);

        $verify_response = $this->verify($input, $check_assign_mid);

        if (isset($verify_response['data']['attributes']['status']) && $verify_response['data']['attributes']['status'] == 'processed') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';
        } elseif (isset($verify_response['data']['attributes']['status']) && $verify_response['data']['attributes']['status'] == 'process_pending') {
        	$input['status'] = '2';
            $input['reason'] = 'Transaction pending for approval.';
        } elseif (isset($verify_response['data']['attributes']['status']) && $verify_response['data']['attributes']['status'] == 'process_failed') {
        	\Log::info(['everpay_verify_failed' => $verify_response]);
            $input['status'] = '0';
            $input['reason'] = $verify_response['error'][0]['status'] ?? 'Transaction authentication failed.';
        } elseif (isset($verify_response['errors'][0]['status']) && !empty($verify_response['errors'][0]['status'])) {
        	$input['status'] = '0';
            $input['reason'] = $verify_response['errors'][0]['status'];
        } else {
        	\Log::info(['everpay_verify_else' => $verify_response]);
            $input['status'] = '0';
            $input['reason'] = $verify_response['errors']['status'] ?? 'Transaction declined in 3D secure verification.';
        }

        // redirect back to $response_url
        $transaction_response = $this->storeTransaction($input);

        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
	}

	// ================================================
	/* method : callback
	* @param  :
	* @description : callback back from everpay
	*/// ===============================================
	public function callback(Request $request, $session_id)
	{
		$request_data = $request->all();

        $transaction_session = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            // ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->where('is_completed', 0)
            ->first();

        if (empty($transaction_session)) {
            abort(404);
        }

        $input = json_decode($transaction_session->request_data, true);

        $check_assign_mid = checkAssignMid($input['payment_gateway_id']);

        $verify_response = $this->verify($input, $check_assign_mid);

        \Log::info(['$verify_response_callback' => $verify_response]);

        if (isset($verify_response['data']['attributes']['status']) && $verify_response['data']['attributes']['status'] == 'processed') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';
        } elseif (isset($verify_response['data']['attributes']['status']) && $verify_response['data']['attributes']['status'] == 'process_pending') {
        	$input['status'] = '2';
            $input['reason'] = 'Transaction pending for approval.';
        } elseif (isset($verify_response['data']['attributes']['status']) && $verify_response['data']['attributes']['status'] == 'process_failed') {
        	\Log::info(['everpay_verify_failed' => $verify_response]);
            $input['status'] = '0';
            $input['reason'] = $verify_response['error'][0]['status'] ?? 'Transaction authentication failed.';
        } elseif (isset($verify_response['errors'][0]['status']) && !empty($verify_response['errors'][0]['status'])) {
        	$input['status'] = '0';
            $input['reason'] = $verify_response['errors'][0]['status'];
        } else {
        	\Log::info(['everpay_verify_else' => $verify_response]);
            $input['status'] = '0';
            $input['reason'] = $verify_response['errors']['status'] ?? 'Transaction declined in 3D secure verification.';
        }

        // redirect back to $response_url
        $transaction_response = $this->storeTransaction($input);

        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
	}

	// ================================================
	/* method : verify
	* @param  :
	* @description : verify transaction
	*/// ===============================================
	public function verify($input, $check_assign_mid)
	{
		$verify_url = self::BASE_URL.'/payment-invoices/'.$input['gateway_id'];

		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $verify_url);
        curl_setopt($ch, CURLOPT_USERPWD, $check_assign_mid->account_id.':'.$check_assign_mid->password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $verify_body = curl_exec($ch);

        curl_close ($ch);

        $verify_array = json_decode($verify_body, 1);

        return $verify_array;
	}
}
