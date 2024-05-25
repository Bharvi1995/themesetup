<?php

/**
 * 
 * All null values will be replace by user input.
 * 
 * */
return [
    'payin_fields' => [
        'paymentMethod' => [
            'type' => null, // DebitCard, CreditCard, Cash, BankTransfer
            'code' => null, // https://docs.v3.localpayment.com/api-reference-guide/payment-methods-codes/argentina
            // 'flow' => 'DIRECT',
        ],
        'externalId' => null,
        'country' => null,
        'amount' => null,
        'currency' => null,
        'accountNumber' => null,
        'conceptCode' => '0039', // Transaction concept code, from available concept codes. Based on our experience, most usual concept codes are 0039 (remittances) and 0040 (corporate payments).
        // 'comment' => null,
        'beneficiary' => [
            'merchant' => 'chargemoney',
            'type' => 'INDIVIDUAL',
            'name' => 'chargemoney',
            'lastname' => 'chargemoney',
            'document' => [
                'type' => 'DNI', // https://docs.v3.localpayment.com/api-reference-guide/document-validations
                'id' => '12345678'
            ],
            // 'userReference' => 'abc123',
            // 'email' => null,
            // 'phone' => [
            //     'countryCode' => '54',
            //     'areaCode' => '11',
            //     'number' => '98789632'
            // ],
            // 'birthdate' => '2000-01-01',
            // 'nationality' => 'Argentinian',
            // 'address' => [
            //     'street' => 'Charruas',
            //     'number' => '938',
            //     'city' => null,
            //     'state' => null,
            //     'country' => null,
            //     'zipCode' => null,
            //     'comment' => 'portero 801'
            // ]
        ],
        'payer' => [
            'type' => 'INDIVIDUAL', // COMPANY, INDIVIDUAL
            'name' => null,
            'lastname' => null,
            'document' => [
                'id' => '12345678', // https://docs.v3.localpayment.com/api-reference-guide/document-validations
                'type' => 'DNI'
            ],
            'email' => null,
            'phone' => [
                'countryCode' => null,
                'areaCode' => null,
                'number' => null
            ],

            'address' => [
                'street' => null,
                // 'number' => '938',
                'city' => null,
                'state' => null,
                'country' => null,
                'zipCode' => null
            ]
            // 'userReference' => 'abcdef1',
        ],
        'card' => [
            'name' => null,
            'number' => null,
            'cvv' => null,
            'expirationMonth' => null,
            'expirationYear' => null,
            'installments' => 1 // card.installments must be equals or greater than 1
        ]
    ]
];
