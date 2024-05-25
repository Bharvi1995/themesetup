<?php

namespace App\Providers;

use App\PaymentGateways\PaymentGatewayContract;
use App\PaymentGateways\StripePaymentGateway;
use App\PaymentGateways\WonderlandPaymentGateway;
use Illuminate\Support\ServiceProvider;

class PaymentGatewayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // $this->app->singleton(PaymentGatewayContract::class, function () {
        //     switch (config('custom.payment.gateway')) {
        //         case 'stripe' || 'testmid':
        //             return new StripePaymentGateway();
        //             break;
        //         case 'wonderland':
        //             return new WonderlandPaymentGateway();
        //             break;

        //         default:
        //             throw new \Exception('Please provide valid payment gateway.');
        //     }
        // });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
