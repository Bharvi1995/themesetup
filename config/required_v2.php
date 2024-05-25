<?php

return [

	/*
    |--------------------------------------------------------------------------
    | Required fields
    |--------------------------------------------------------------------------
    |
    | These fields are visible in MID gateway input
    |
    */
    'validate' => [
        'first_name' => 'required|min:2|max:100|regex:/^[a-zA-Z\s]+$/',
        'last_name' => 'required|min:2|max:100|regex:/^[a-zA-Z\s]+$/',
        'address' => 'required|min:2|max:250',
        'country' => 'required|max:2|min:2|regex:(\b[A-Z]+\b)',
        'state' => 'required|min:2|max:250',
        'city' => 'required|min:2|max:250',
        'zip' => 'required|min:2|max:250',
        'ip_address' => 'required|ip',
        'customer_order_id' => 'nullable|max:100',
        'email' => 'required|email',
        'phone_no' => 'required|min:5|max:20',
        'amount' => 'required|regex:/^\d+(\.\d{1,9})?$/',
        'currency' => 'required|max:3|min:3|regex:(\b[A-Z]+\b)',
        'card_no' => 'required|min:12|max:24',
        'ccExpiryMonth' => 'required|numeric|min:1|max:12',
        'ccExpiryYear' => 'required|numeric|min:2022|max:2045',
        'cvvNumber' => 'required|numeric|min:0|max:9999',
        'response_url' => 'required|url',
        'webhook_url' => 'nullable|url',
    ],

    'required_all_fields' => [
        'first_name',
        'last_name',
        'address',
        'customer_order_id',
        'country',
        'state',
        'city',
        'zip',
        'email',
        'phone_no',
        'amount',
        'currency',
        'descriptor',
        'response_url',
        'webhook_url',
    ],
];
