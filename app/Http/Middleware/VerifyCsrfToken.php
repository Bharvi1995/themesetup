<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Symfony\Component\HttpFoundation\Cookie;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'accept_url',
        'threeDsecureReturn',
        'processing-return-url',
        'hosted-voguepay-payment-redirect',
        'hosted-pay/payment-request',
        'hosted-pay/input-card/*',
        'hosted-vougepay/payment-request',
        'redirect-after-payment',
        'redirect-after-payment-api',
        'hosted-pay',
        'payment/test-transaction/*',
        'wonderland-checkout/response/*',
        'gatewayservice-return',
        'secure-gateway/notify/*',
        'order-verify-page-submit',
        'checkout-form/*',
        'v2/checkout-form/*',
        'opay/waiting-submit/*',
        'opay/input-submit/*',
        'opay/callback',
        'onlinenaira-notify',
        'triplea-webhook-url',
        'wyre/form-submit',
        'wyre/callback/notify',
        'bitbaypay/notify',
        'cryptoxa/callback/*',
        'interkassa/callback/*',
        'interkassa/success/*',
        'interkassa/fail/*',
        'paycos/callback/*',
        'trust/notification/*',
        'opennode-callbackUrl/*',
        'interkassa-upi/success/*',
        'interkassa-upi/fail/*',
        'interkassa-net-banking/success/*',
        'interkassa-net-banking/fail/*',
        'vippass/callback/*',
        'vippass/webhook/*',
        'qartpay/callback/*',
        'qartpay/callback/upi/*',
        'paythone/callback',
        'chakra/callback/*',
        'facilitapay/webhook',
        'cellulant/success/*',
        'cellulant/fail/*',
        'cellulant/webhook/*',
        'qikpay/callback/*',
        'qikpay/upi/callback/*',
        'avalanchepay/api',
        'paybypago/notification/*',
        'takepayment-redirect/*',
        'takepayment-callback/*',
        'nihaopay/notification/*',
        'wonderland/notification/*',
        'pay-genius/redirect/*',
        'pay-genius/notify/*',
        'notification/secure-epayment',
        'wonderlandvisa/notify/*',
        'paythrone/webhook',
        'secureepayments/webhook',
        'altercards/webhook',
        'transactworld/callback/*',
        'ezipay/callback',
        'qartpays2s/callback/*',
        'winopay/callback',
        'qikpays2s/callback/*',
        'gtpay/callback',
        'dixonpayvisa/return/*',
        'dixonpayvisa/notify/*',
        'aiglobalpay/return/*',
        'aiglobalpay/notify/*',
        'boombill/webhook/*',
        'basqet/payment-received',
        'kora-pay/webhook/*',
        'milkypay/callback/*',
        'soi/webhook/*',
        'soihosted/webhook/',
        'carppay/notify/*',
        'damapay/api',
        'fcfpay/callback',
        's/callback/*',
        'royalpay/success/*',
        'royalpay/fail/*',
        'royalpay/pending/*',
        'kiparis/callback/*',
        'kiparis/success/*',
        'kiparis/fail/*',
        'kiparis/pending/*',
        'amlnode/callback',
        'fibonatix/callback/*',
        'fibonatix/redirect/*',
        'xchange/return/*',
        'xchange/notify/*',
        'paypound/webhook/*',
        '4on/notify/*',
        'payecards/notify',
        'payecards/return/*',
        'thepayingspot/callback/*',
        'attitudepay/*',
        "carespay/*",
        "carespay/callback/*",
        "cryp/webhook/*",
        "centpays/callback/*",
        "centpays/webhook*",
        "tomipay/*",
        "zoftpay/callback/*",
        "zoftpay/webhook/*",
        "cashenvoy-webhook/*",
        "infipay-webhook/*",
        "payaza/callback/*",
        "highisk-webhook/*",
        "payzentric/webhook",
        "kiwipay/webhook",
        "leonepay/webhook/*",
        "yelopay/return/*",
        "monnet/redirect/*",
        "monnet/callback/receive",
        "redfern/callback/*",
        "yelopay/3ds/callback",
        "dasshpe/return/*",
        "ems/callback/*",
        "ems/return/*",
        "coinspaid/webhook",
        "dasshpeupi/return/*",
        "epsi/webhook/*",
        "cryptoxamax/callback",
        "startbutton/webhook",
        "fnpo/webhook/*",
        "faci/redirect/*",
        "faci/webhook/*",
        "kpentag/webhook/*",
        'uzopay/webhook/*',
        'securepay/webhook/',
        'chargemoney/callback/*'
    ];

    /**
     * Add the CSRF token to the response cookies.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function addCookieToResponse($request, $response)
    {
        $config = config('session');

        $response->headers->setCookie(
            new Cookie(
                'XSRF-TOKEN',
                $request->session()->token(),
                $this->availableAt(60 * $config['lifetime']),
                $config['path'],
                $config['domain'],
                $config['secure'],
                true,
                true,
                $config['same_site'] ?? null
            )
        );
        return $response;
    }
}