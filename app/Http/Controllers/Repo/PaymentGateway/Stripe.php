<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use Exception;
use App\Transaction;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;

class Stripe extends Controller
{
	use StoreTransaction;

	const BASE_URL = 'https://api.stripe.com';

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
	* @description : 
	*/// ===============================================
	public function checkout($input, $check_assign_mid)
	{
        // create payment method
        $payment_url = self::BASE_URL.'/v1/payment_methods';
        $secret_key = $check_assign_mid->secret_key;

        $payment_data = [
            'type' => 'card',
            'card[number]' => $input['card_no'],
            'card[exp_month]' => $input['ccExpiryMonth'],
            'card[exp_year]' => $input['ccExpiryYear'],
            'card[cvc]' => $input['cvvNumber'],
            'billing_details[address][city]' => $input['city'],
            'billing_details[address][state]' => $input['state'],
            'billing_details[address][country]' => $input['country'],
            'billing_details[address][line1]' => $input['address'],
            'billing_details[address][postal_code]' => $input['zip'],
            'billing_details[email]' => $input['email'],
            'billing_details[name]' => $input['first_name'].' '.$input['last_name'],
            'billing_details[phone]' => $input['phone_no'],
        ];

        $payment_payload = http_build_query($payment_data);

        $payment_headers = [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer '.$secret_key
        ];

        $payment_body = $this->curlPost($payment_url, $payment_payload, $payment_headers);

        $payment_response = json_decode($payment_body, true);

        // create payment intent
        if (isset($payment_response['id']) && $payment_response['id'] != null) {

            // update session data
            $input['gateway_id'] = $payment_response['id'];
            $this->updateGatewayResponseData($input, $payment_response);
            
            $request_url = self::BASE_URL.'/v1/payment_intents';

            $request_data = [
                'amount' => $input['converted_amount'] * 100,
                'currency' => $input['currency'],
                'payment_method_types[]' => 'card',
                'payment_method' => $payment_response['id'],
                'confirm' => 'true',
                'capture_method' => 'automatic',
                'return_url' => route('stripe.return', $input['session_id']),
                'payment_method_options[card][request_three_d_secure]' => 'automatic',
            ];

            $request_payload = http_build_query($request_data);

            $request_headers = [
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer '.$secret_key
            ];

            $response_body = $this->curlPost($request_url, $request_payload, $request_headers);

            $response_data = json_decode($response_body, true);

            // 3ds secure link
            if (isset($response_data['next_action']['redirect_to_url']['url']) && $response_data['next_action']['redirect_to_url']['url'] != null) {

                return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                    'redirect_3ds_url' => $response_data['next_action']['redirect_to_url']['url'],
                ];

            // transaction success
            } elseif (isset($response_data['status']) && $response_data['status'] == 'succeeded') {
                
                return [
                    'status' => '1',
                    'reason' => 'Your transaction has been processed successfully.',
                    'order_id' => $input['order_id'],
                ];

            // transaction decline
            } elseif (isset($response_data['error']['message']) && $response_data['error']['message'] != null) {

                return [
                    'status' => '0',
                    'reason' => $response_data['error']['message'],
                    'order_id' => $input['order_id'],
                ];

            } else {

                \Log::info(['stripe_else' => $response_data]);
                return [
                    'status' => '0',
                    'reason' => 'Your transaction could not processed.',
                    'order_id' => $input['order_id'],
                ];
            }
        } elseif (isset($payment_response['error']['message']) && $payment_response['error']['message'] != null) {

            return [
                'status' => '0',
                'reason' => $payment_response['error']['message'],
                'order_id' => $input['order_id'],
            ];

        }
    }

    // ================================================
    /* method : return
    * @param  : 
    * @Description : return from stripe 3ds
    */// ==============================================
    public function return(Request $request, $session_id)
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

        $secret_key = $check_assign_mid->secret_key;

        if (isset($request_data['payment_intent']) && $request_data['payment_intent'] != null) {

            $get_url = self::BASE_URL.'/v1/payment_intents/'.$request_data['payment_intent'];

            $get_headers = [
                'Authorization: Bearer '.$secret_key
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $get_url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $get_headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $get_response = curl_exec($ch);

            curl_close ($ch);

            $get_data = json_decode($get_response, 1);

            if (isset($get_data['status']) && $get_data['status'] == 'succeeded') {
                
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccessed successfully.';

            } elseif (isset($get_data['error']['message']) && $get_data['error']['message'] != null) {
                $input['status'] = '0';
                $input['reason'] = $get_data['error']['message'];

            } else {
                $input['status'] = '0';
                $input['reason'] = 'Your transaction could not processed.';
            }

            // redirect back to $response_url
            $transaction_response = $this->storeTransaction($input);

            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        } else {
            abort(404);
        }
    }

    // ================================================
    /* method : curlPost
    * @param  : 
    * @description : curl request
    */// ===============================================
    private function curlPost($url, $data, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close ($ch);

        return $response;
    }
}