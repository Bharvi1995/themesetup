<?php

namespace App\PaymentGateways;

use Illuminate\Support\Facades\Log;

class StripePaymentGateway extends PaymentGateway implements PaymentGatewayContract
{
    const BASE_URL = 'https://api.stripe.com';

    // dev method, should be removed
    public function charge()
    {
        return __CLASS__;
    }

    public function transaction($input, $check_assign_mid)
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
                'return_url' => route('test-stripe', $input['session_id']),
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
}
