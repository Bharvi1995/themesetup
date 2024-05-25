<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Mail;
use Session;
use Exception;
use App\User;
use App\Transaction;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Cartalyst\Stripe\Laravel\Facades\Stripe;


class Stripes extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://api.stripe.com';

    // ================================================
    /* method : stripeForm
    * @param  :
    * @Description : Load stripe test form
    */// ==============================================
    public function checkout($input, $check_assign_mid)
    {
        // create payment method
        $payment_url = self::BASE_URL . '/v1/payment_methods';

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
            'billing_details[name]' => $input['first_name'] . ' ' . $input['last_name'],
            'billing_details[phone]' => $input['phone_no'],
        ];
        
        $payment_payload = http_build_query($payment_data);

        $payment_headers = [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer ' . $check_assign_mid->secret_key
        ];

        $payment_body = curlPost($payment_url, $payment_payload, $payment_headers);

        $payment_response = json_decode($payment_body, true);

        // create payment intent
        if (isset($payment_response['id']) && $payment_response['id'] != null) {
            $request_url = self::BASE_URL . '/v1/payment_intents';

            $request_data = [
                'amount' => $input['converted_amount'] * 100,
                'currency' => $input['converted_currency'],
                'payment_method_types[]' => 'card',
                'payment_method' => $payment_response['id'],
                'confirm' => 'true',
                'capture_method' => 'automatic',
                'return_url' => route('return-stripe', $input['session_id']),
                'payment_method_options[card][request_three_d_secure]' => 'automatic',
            ];

            $request_payload = http_build_query($request_data);

            $request_headers = [
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer ' . $check_assign_mid->secret_key
            ];

            $response_body = curlPost($request_url, $request_payload, $request_headers);

            $response_data = json_decode($response_body, true);
            if (isset($response_data['next_action']['redirect_to_url']['url']) && $response_data['next_action']['redirect_to_url']['url'] != null) {
                // redirect 3ds page
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                    'redirect_3ds_url' => $response_data['next_action']['redirect_to_url']['url'],
                ];
            } elseif (isset($response_data['status']) && $response_data['status'] == 'succeeded') {
                return [
                    'status' => '1',
                    'reason' => 'Your transaction has been processed successfully.',
                    'order_id' => $input['order_id'],
                ];
            } elseif (isset($response_data['error']['message']) && $response_data['error']['message'] != null) {
                return [
                    'status' => '0',
                    'reason' => $response_data['error']['message'],
                    'order_id' => $input['order_id'],
                ];
            } else {
                Log::info(['stripe_else' => $response_data]);

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

    public function stripeForm(Request $request, $session_id)
    {
        $request_data = $request->all();

        $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first();

        if ($transaction_session == null) {
            return response()->json(['message' => 'Transaction not found.']);
        }

        $request_data = json_decode($transaction_session->request_data, 1);

        return view('gateway.test3dSecure', compact('session_id', 'request_data'));
    }

    // ================================================
    /* method : test3DSFormSubmit
    * @param  :
    * @Description : submit the test 3DS form
    */// ==============================================
    public function test3DSFormSubmit(Request $request, $session_id)
    {

        $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first();

        if ($transaction_session == null) {
            return response()->json(['message' => 'Transaction not found.']);
        }

        $input = json_decode($transaction_session->request_data, 1);
        $input['reason'] = $request->transaction_response;
        if (isset($request->transaction_response) && $request->transaction_response == 'Approved') { 
            $input['status'] = '1';
        } else if(isset($request->transaction_response) && $request->transaction_response == 'Pending') {
            $input['status'] = '2';
        } else {
            $input['status'] = '0';
        }
    
        // store transaction
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }
}
