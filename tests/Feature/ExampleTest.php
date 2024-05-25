<?php

namespace Tests\Feature;

use App\Helpers\PaymentResponse;
use App\PaymentGateways\PaymentGatewayContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    function kebabToHumanString($str)
    {
        return ucwords(str_replace('-', ' ', $str));
    }

    public function testBasicTest()
    {
        $response = PaymentResponse::make();

        $response->setStatus(false)->setMessage('Error response')->setExtraParams([
            'test' => 'test'
        ])->setResponseCode(400);


        dd($response->getResponse());



        $fields = [
            'user_id', 'order_id', 'session_id', 'gateway_id', 'first_name', 'last_name', 'address', 'customer_order_id', 'country',
            'state', 'city', 'zip', 'ip_address', 'email', 'phone_no', 'card_type', 'amount', 'amount_in_usd', 'currency', 'card_no',
            'ccExpiryMonth', 'ccExpiryYear', 'cvvNumber', 'status', 'reason', 'descriptor', 'payment_gateway_id', 'payment_type',
            'merchant_discount_rate', 'bank_discount_rate', 'net_profit_amount', 'chargebacks', 'chargebacks_date', 'chargebacks_remove',
            'chargebacks_remove_date', 'refund', 'refund_reason', 'refund_date', 'refund_remove', 'refund_remove_date', 'is_flagged', 'flagged_by',
            'flagged_date', 'is_flagged_remove', 'flagged_remove_date', 'is_retrieval', 'retrieval_date', 'is_retrieval_remove',
            'retrieval_remove_date', 'is_converted', 'converted_amount', 'converted_currency', 'is_converted_user_currency', 'converted_user_amount',
            'converted_user_currency', 'website_url_id', 'request_from_ip', 'request_origin', 'is_request_from_vt', 'is_transaction_type',
            'is_webhook', 'response_url', 'webhook_url', 'webhook_status', 'webhook_retry', 'transactions_token', 'bin_details', 'transaction_hash',
            'is_duplicate_delete', 'transaction_date',
        ];

        dd(count($fields));
        Config::set('custom.payment.gateway', 'stripe');

        dd(app(PaymentGatewayContract::class)->charge());



        $test = $this->snakeToCamel('hello-world');
        dd($test);
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
