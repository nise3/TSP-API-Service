<?php

use App\Models\PaymentTransactionHistory;

return [
    "is_sand_box" => env('IS_SANDBOX', false),
    "debug" => env('EK_PAY_DEBUG', false),
    "trnx_currency" => 'BDT',
    "sand_box" => [
        "ekpay_base_uri" => 'https://sandbox.ekpay.gov.bd/ekpaypg/v1',
        'mac_addr' => '1.1.1.1',
        'mer_info' => [
            'mer_reg_id' => 'nise_test',
            'mer_pas_key' => 'NiSe@TsT11'
        ]
    ],
    "production" => [
        "ekpay_base_uri" => 'https://pg.ekpay.gov.bd/ekpaypg/v1',
        PaymentTransactionHistory::PAYMENT_PURPOSE_COURSE_ENROLLMENT => [
            'mac_addr' => '180.148.214.186',
            'mer_info' => [
                'mer_reg_id' => 'nise_mer',
                'mer_pas_key' => 'NiscE@ekP02'
            ]
        ],
        PaymentTransactionHistory::PAYMENT_PURPOSE_E_RPL_CERTIFICATION_FREE => [
            'mac_addr' => '180.148.214.186',
            'mer_info' => [
                'mer_reg_id' => 'nise_mer',
                'mer_pas_key' => 'NiscE@ekP02'
            ]
        ],
    ]
];
