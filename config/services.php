<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => 'pk_live_lnVXpGEq3DzsqQ2QIFomuoSH',
        'secret' => 'sk_live_P23dZIlAbNE1ijfmbrZqIsZ1',
    ],
    'neutrino' => [
        'user_id' => env('NEUTRINO_USER_ID', null),
        'api_key' => env('NEUTRINO_API_KEY', null),
    ],
    'viva' => [
        // 'api_key' => env('VIVA_API_KEY'),
        // 'merchant_id' => env('VIVA_MERCHANT_ID'),
        // 'public_key' => env('VIVA_PUBLIC_KEY'),
        // 'environment' => env('VIVA_ENVIRONMENT', 'demo'),
        'api_key' => 'z=:T)L',
        'merchant_id' => '91de837d-4121-4e7d-b385-23e200efd004',
        'public_key' => 'VwT4ennzb1ejc5FBN1jmvgiBVJC9FJmLtiQ6nB8clqg=',
        'environment' => 'production',
    ],

    'firebase' => [
        'notification_url' => env('FIREBASE_NOTIFICATION_URL', null),
        'server_key' => env('FIREBASE_SERVER_KEY', null),
        'url' => env('FIREBASE_URL', null),

        'api_key' => env('FIREBASE_API_KEY', null),
        'auth_domain' => env('FIREBASE_AUTH_DOMAIN', null),
        'database_url' => env('FIREBASE_DATABASE_URL', null),
        'project_id' => env('FIREBASE_PROJECT_ID', null),
        'storage_bucket' => env('FIREBASE_STORAGE_BUCKET', null),
        'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID', null),
        'app_id' => env('FIREBASE_APP_ID', null),
    ],

    'neutrino' => [
        'user_id' => env('NEUTRINO_USER_ID', null),
        'api_key' => env('NEUTRINO_API_KEY', null),
    ],

    'currency_layer' => [
        'api_key' => env('CURRENCY_LAYER_API_KEY', null),
    ],

    'test_cards' => [
        "4242424242424242",
        "4000000000000077",
        "4000056655665556",
        "5555555555554444",
        "2223003122003222",
        "5200828282828210",
        "5105105105105100",
        "378282246310005",
        "371449635398431",
        "6011111111111117",
        "6011000990139424",
        "30569309025904",
        "38520000023237",
        "3566002020360505",
        "6200000000000005",
    ],

];
