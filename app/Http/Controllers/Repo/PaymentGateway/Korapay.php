<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\User;
use App\TransactionSession;
use App\Transaction;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Korapay Extends Controller
{
	use StoreTransaction;

	const BASE_URL = 'https://api.korapay.com/merchant';

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
		// create card payment request
		$payload_response = $this->cardPayment($input, $check_assign_mid);

		$input['gateway_id'] = $payload_response['data']['transaction_reference'] ?? null;

		// update session-data
		$this->updateGatewayResponseData($input, $payload_response);

		if (isset($payload_response['data']['status']) && $payload_response['data']['status'] == 'success') {
			$input['status'] = '1';
            $input['reason'] = 'Your transaction was processed successfully.';

            return $input;
		} elseif (isset($payload_response['data']['status']) && $payload_response['data']['status'] == false) {
			$input['status'] = '0';
            $input['reason'] = $payload_response['message'] ?? 'Transaction initialization failed.';

            return $input;
		} elseif (isset($payload_response['data']['status']) && $payload_response['data']['status'] == 'failed') {
			$input['status'] = '0';
            $input['reason'] = $payload_response['message'] ?? 'Transaction authorization failed.';

            return $input;
		} elseif (isset($payload_response['data']['status']) && $payload_response['data']['status'] == 'processing') {

			// 3ds required
			if (
				isset($payload_response['data']['auth_model']) && $payload_response['data']['auth_model'] == '3DS' &&
				isset($payload_response['data']['redirect_url']) && !empty($payload_response['data']['redirect_url'])
				// isset($payload_response['data']['authorization']['redirect_url']) && !empty($payload_response['data']['authorization']['redirect_url'])
			) {
				return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect.',
                    'redirect_3ds_url' => $payload_response['data']['redirect_url']
                    // 'redirect_3ds_url' => $payload_response['data']['authorization']['redirect_url']
                ];
			// avs required
			} elseif (isset($payload_response['data']['auth_model']) && $payload_response['data']['auth_model'] == 'AVS') {

				$auth_response = $this->cardAuthorizeAVS($input, $check_assign_mid);

			// phone_no required
			} elseif (isset($payload_response['data']['auth_model']) && $payload_response['data']['auth_model'] == 'CARD_ENROLL') {

				$auth_response = $this->cardAuthorizePhone($input, $check_assign_mid);

			// pin required
			} elseif (isset($payload_response['data']['auth_model']) && $payload_response['data']['auth_model'] == 'PIN') {

				return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect.',
                    'redirect_3ds_url' => route('korapay.pin', $input['session_id']),
                ];

			// otp required
			} elseif (isset($payload_response['data']['auth_model']) && $payload_response['data']['auth_model'] == 'OTP') {

				return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect.',
                    'redirect_3ds_url' => route('korapay.otp', $input['session_id']),
                ];

			} else {
				\Log::info(['Korapay_processing_else' => $payload_response]);

				$input['status'] = '0';
	            $input['reason'] = $payload_response['message'] ?? 'Transaction authorization failed.';

	            return $input;
			}

			// update session-data
			$this->updateGatewayResponseData($input, $auth_response);

			// auth response after pin/avs/otp
			if (isset($auth_response['data']['status']) && $auth_response['data']['status'] == 'success') {
				$input['status'] = '1';
	            $input['reason'] = 'Your transaction was processed successfully.';

	            return $input;
			} elseif (isset($auth_response['data']['status']) && $auth_response['data']['status'] == 'failed') {
				$input['status'] = '0';
	            $input['reason'] = $auth_response['message'] ?? 'Transaction authorization failed.';

	            return $input;
			} elseif (isset($auth_response['data']['status']) && $auth_response['data']['status'] == 'processing') {

				// 3ds required
				if (
					isset($auth_response['data']['auth_model']) && $auth_response['data']['auth_model'] == '3DS' &&
					isset($auth_response['data']['redirect_url']) && !empty($auth_response['data']['redirect_url'])
					// isset($auth_response['data']['authorization']['redirect_url']) && !empty($auth_response['data']['authorization']['redirect_url'])
				) {
					return [
	                    'status' => '7',
	                    'reason' => '3DS link generated successfully, please redirect.',
	                    'redirect_3ds_url' => $auth_response['data']['redirect_url']
	                    // 'redirect_3ds_url' => $auth_response['data']['authorization']['redirect_url']
	                ];
				
				// pin required
				} elseif (isset($auth_response['data']['auth_model']) && $auth_response['data']['auth_model'] == 'PIN') {

					return [
	                    'status' => '7',
	                    'reason' => '3DS link generated successfully, please redirect.',
	                    'redirect_3ds_url' => route('korapay.pin', $input['session_id']),
	                ];

				// otp required
				} elseif (isset($auth_response['data']['auth_model']) && $auth_response['data']['auth_model'] == 'OTP') {

					return [
	                    'status' => '7',
	                    'reason' => '3DS link generated successfully, please redirect.',
	                    'redirect_3ds_url' => route('korapay.otp', $input['session_id']),
	                ];

				} else {
					\Log::info(['Korapay_processing_else' => $auth_response]);

					$input['status'] = '0';
		            $input['reason'] = $auth_response['message'] ?? 'Transaction authorization failed.';

		            return $input;
				}

			} else {

				\Log::info(['korapay_card_reauth_else' => $auth_response]);
				$input['status'] = '0';
	            $input['reason'] = $auth_response['message'] ?? 'Transaction authorization failed.';

	            return $input;

			}

		} else {
			\Log::info(['korapay_card_auth_else' => $payload_response]);
			$input['status'] = '0';
            $input['reason'] = $payload_response['message'] ?? 'Transaction authorization failed.';

            return $input;
		}
	}

	// ================================================
	/* method : cardPayment
	* @param  : 
	* @description : create card payment api
	*/// ===============================================
	private function cardPayment($input, $check_assign_mid)
	{
		$payload_url = self::BASE_URL.'/api/v1/charges/card';

		$payload_headers = [
			'Authorization: Bearer '.$check_assign_mid->secret_key,
			'Content-Type: application/json',
		];

		$payload_request = [
			'reference' => $input['session_id'],
			'card' => [
				'number' => $input['card_no'],
				'cvv' => $input['cvvNumber'],
				'expiry_month' => $input['ccExpiryMonth'],
				'expiry_year' => substr($input['ccExpiryYear'], -2),
			],
			'amount' => $input['converted_amount'],
			'currency' => $input['converted_currency'],
			'redirect_url' => route('korapay.redirect', $input['session_id']),
			'customer' => [
				'name' => $input['first_name'].' '.$input['last_name'],
				'email' => $input['email'],
			],
		];

		// encrypt data
		$payload_encrypt = $this->encryptAES256($check_assign_mid->encryption_key, json_encode($payload_request));

		$encrypt_req_data = ['charge_data' => $payload_encrypt];
		$encrypt_json_data = json_encode($encrypt_req_data);

		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $payload_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encrypt_json_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $payload_headers);

        $payload_body = curl_exec($ch);

        curl_close ($ch);

        $payload_response =  json_decode($payload_body, 1);

		return $payload_response;
	}

	// ================================================
	/* method : cardAuthorizeAVS
	* @param  : 
	* @description : card authorization avs
	*/// ===============================================
	private function cardAuthorizeAVS($input, $check_assign_mid)
	{
		$avs_url = self::BASE_URL.'/api/v1/charges/card/authorize';

		$avs_headers = [
			'Authorization: Bearer '.$check_assign_mid->secret_key,
			'Content-Type: application/json',
		];

		$avs_data = [
			'transaction_reference' => $input['gateway_id'],
			'authorization' => [
				'avs' => [
					'state' => $input['state'],
					'city' => $input['city'],
					'country' => $input['country'],
					'address' => $input['address'],
					'zip_code' => $input['zip'],
				],
			],
		];

		$avs_json = json_encode($avs_data);

		$avs_response = $this->curlPost($avs_url, $avs_json, $avs_headers);

		return $avs_response;
	}

	// ================================================
	/* method : cardAuthorizePhone
	* @param  : 
	* @description : card authorization phone
	*/// ===============================================
	private function cardAuthorizePhone($input, $check_assign_mid)
	{
		$phone_url = self::BASE_URL.'/api/v1/charges/card/authorize';

		$phone_headers = [
			'Authorization: Bearer '.$check_assign_mid->secret_key,
			'Content-Type: application/json',
		];

		$phone_data = [
			'transaction_reference' => $input['gateway_id'],
			'authorization' => [
				'phone' => $input['phone_no'],
			],
		];

		$phone_json = json_encode($phone_data);

		$phone_response = $this->curlPost($phone_url, $phone_json, $phone_headers);

		return $phone_response;
	}

	// ================================================
	/* method : pin
	* @param  : 
	* @description : pin input form
	*/// ===============================================
	public function pin($session_id)
	{
        $transaction_session = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first();

        if (empty($transaction_session)) {
            return abort(404);
        }
        
        return view('gateway.korapay.pin', compact('session_id'));
    }

	// ================================================
	/* method : pinSubmit
	* @param  : 
	* @description : pin submit
	*/// ===============================================
	public function pinSubmit(Request $request, $session_id)
	{
		$this->validate($request, [
			'pin' => 'required|numeric'
		]);

		$pin = $request->pin;

		$transaction_session = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->where('is_completed', 0)
            ->first();

        if (empty($transaction_session)) {
            abort(404);
        }

        $input = json_decode($transaction_session->request_data, true);

        $check_assign_mid = checkAssignMid($input['payment_gateway_id']);

		$auth_url = self::BASE_URL.'/api/v1/charges/card/authorize';

		$auth_headers = [
			'Authorization: Bearer '.$check_assign_mid->secret_key,
			'Content-Type: application/json',
		];

		$auth_data = [
			'transaction_reference' => $input['gateway_id'],
			'authorization' => [
				'pin' => $pin,
			],
		];

		$auth_json = json_encode($auth_data);

		$auth_response = $this->curlPost($auth_url, $auth_json, $auth_headers);

		// auth response after pin
		if (isset($auth_response['data']['status']) && $auth_response['data']['status'] == 'success') {
			$input['status'] = '1';
            $input['reason'] = 'Your transaction was processed successfully.';
		} elseif (isset($auth_response['data']['status']) && $auth_response['data']['status'] == 'failed') {
			$input['status'] = '0';
            $input['reason'] = $auth_response['message'] ?? 'Transaction authorization failed.';
		} elseif (isset($auth_response['data']['status']) && $auth_response['data']['status'] == 'processing') {

			// 3ds required
			if (
				isset($auth_response['data']['auth_model']) && $auth_response['data']['auth_model'] == '3DS' &&
				isset($auth_response['data']['redirect_url']) && !empty($auth_response['data']['redirect_url'])
				// isset($auth_response['data']['authorization']['redirect_url']) && !empty($auth_response['data']['authorization']['redirect_url'])
			) {
				return redirect()->away($auth_response['data']['redirect_url']);
				// return redirect()->away($auth_response['data']['authorization']['redirect_url']);

			// otp required
			} elseif (isset($auth_response['data']['auth_model']) && $auth_response['data']['auth_model'] == 'OTP') {
				return redirect()->route('korapay.otp', $input['session_id']);
			} else {
				\Log::info(['korapay_redirect_processing_else' => $auth_response]);
				$input['status'] = '0';
	            $input['reason'] = $auth_response['message'] ?? 'Transaction authorization failed.';
			}

		} else {
			$input['status'] = '0';
            $input['reason'] = $auth_response['message'] ?? 'Transaction authorization failed.';
		}

		// redirect back to $response_url
        $transaction_response = $this->storeTransaction($input);

        $store_transaction_link = $this->getRedirectLink($input);

        return redirect($store_transaction_link);
	}

	// ================================================
	/* method : otp
	* @param  : 
	* @description : otp input form
	*/// ===============================================
	public function otp($session_id)
	{
        $transaction_session = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first();

        if (empty($transaction_session)) {
            return abort(404);
        }
        
        return view('gateway.korapay.otp', compact('session_id'));
    }

	// ================================================
	/* method : otpSubmit
	* @param  : 
	* @description : otp
	*/// ===============================================
	public function otpSubmit(Request $request, $session_id)
	{
		$this->validate($request, [
			'otp' => 'required|numeric'
		]);

		$otp = $request->otp;

		$transaction_session = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->where('is_completed', 0)
            ->first();

        if (empty($transaction_session)) {
            abort(404);
        }

        $input = json_decode($transaction_session->request_data, true);

        $check_assign_mid = checkAssignMid($input['payment_gateway_id']);

		$auth_url = self::BASE_URL.'/api/v1/charges/card/authorize';

		$auth_headers = [
			'Authorization: Bearer '.$check_assign_mid->secret_key,
			'Content-Type: application/json',
		];

		$auth_data = [
			'transaction_reference' => $input['gateway_id'],
			'authorization' => [
				'otp' => $otp,
			],
		];

		$auth_json = json_encode($auth_data);

		$auth_response = $this->curlPost($auth_url, $auth_json, $auth_headers);

		// auth response after otp
		if (isset($auth_response['data']['status']) && $auth_response['data']['status'] == 'success') {
			$input['status'] = '1';
            $input['reason'] = 'Your transaction was processed successfully.';
		} elseif (isset($auth_response['data']['status']) && $auth_response['data']['status'] == 'failed') {
			$input['status'] = '0';
            $input['reason'] = $auth_response['message'] ?? 'Transaction authorization failed.';
		} elseif (isset($auth_response['data']['status']) && $auth_response['data']['status'] == 'processing') {

			// 3ds required
			if (
				isset($auth_response['data']['auth_model']) && $auth_response['data']['auth_model'] == '3DS' &&
				isset($auth_response['data']['redirect_url']) && !empty($auth_response['data']['redirect_url'])
				// isset($auth_response['data']['authorization']['redirect_url']) && !empty($auth_response['data']['authorization']['redirect_url'])
			) {
				return redirect()->away($auth_response['data']['redirect_url']);
				// return redirect()->away($auth_response['data']['authorization']['redirect_url']);
			// pin required
			} elseif (isset($auth_response['data']['auth_model']) && $auth_response['data']['auth_model'] == 'PIN') {
				return redirect()->route('korapay.pin', $input['session_id']);
			} else {
				\Log::info(['korapay_redirect_processing_else' => $auth_response]);
				$input['status'] = '0';
	            $input['reason'] = $auth_response['message'] ?? 'Transaction authorization failed.';
			}
		} else {
			$input['status'] = '0';
            $input['reason'] = $auth_response['message'] ?? 'Transaction authorization failed.';
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
	private function verify($reference, $check_assign_mid)
	{
		$verify_url = self::BASE_URL.'/api/v1/charges/'.$reference;

		$verify_headers = [
			'Authorization: Bearer '.$check_assign_mid->secret_key,
		];

		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $verify_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $verify_headers);
        
        $verify_body = curl_exec($ch);

        curl_close ($ch);

        $verify_array =  json_decode($verify_body, 1);

        return $verify_array;
	}

	public function redirect(Request $request, $session_id)
	{
		$request_data = $request->all();

        $transaction_session = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->where('is_completed', 0)
            ->first();

        if (empty($transaction_session)) {
            abort(404);
        }

        $input = json_decode($transaction_session->request_data, true);

        $check_assign_mid = checkAssignMid($input['payment_gateway_id']);

		$verify_response = $this->verify($session_id, $check_assign_mid);

		if (isset($verify_response['data']['status']) && $verify_response['data']['status'] == 'success') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';
        } elseif (isset($verify_response['error']['message']) && $verify_response['error']['message'] != null) {
            $input['status'] = '0';
            $input['reason'] = $verify_response['error']['message'];
        } elseif (isset($verify_response['status']) && $verify_response['status'] == false) {
        	$input['status'] = '0';
            $input['reason'] = $verify_response['message'];
        } else {
            $input['status'] = '0';
            $input['reason'] = 'Transaction declined in authentication.';
        }

        // redirect back to $response_url
        $transaction_response = $this->storeTransaction($input);

        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
	}

	public function webhook(Request $request, $session_id)
	{
		$request_data = $request->all();

        $transaction_session = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->where('is_completed', 0)
            ->first();

        if (empty($transaction_session)) {
            abort(404);
        }

        $input = json_decode($transaction_session->request_data, true);

        $check_assign_mid = checkAssignMid($input['payment_gateway_id']);

		$verify_response = $this->verify($session_id, $check_assign_mid);

		if (isset($verify_response['data']['status']) && $verify_response['data']['status'] == 'success') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';
        } elseif (isset($verify_response['error']['message']) && $verify_response['error']['message'] != null) {
            return false;
        } elseif (isset($verify_response['data']['status']) && $verify_response['data']['status'] == false) {
            return false;
        } else {
            return false;
        }

        // redirect back to $response_url
        $transaction_response = $this->storeTransaction($input);

        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
	}

	// ================================================
	/* method : encryptAES256
	* @param  : 
	* @description : encrypt data
	*/// ===============================================
	private function encryptAES256($encryptionKey, $paymentData)
	{
	    $method = "aes-256-gcm";
	    $iv = openssl_random_pseudo_bytes(16);
	    $tag = "";
	    $cipherText = openssl_encrypt($paymentData, $method, $encryptionKey, OPENSSL_RAW_DATA, $iv, $tag, "", 16);
	    return bin2hex($iv).':'.bin2hex($cipherText).':'.bin2hex($tag);
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

        $return_array =  json_decode($response_body, 1);

        return $return_array;
    }
}