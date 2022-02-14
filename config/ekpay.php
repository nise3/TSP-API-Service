<?php
return [
    "is_sand_box" => env('IS_SANDBOX', false),
    "debug" => true,
    "trnx_currency" => 'BDT',
    "sand_box" => [
        "ekpay_base_uri" => 'https://sandbox.ekpay.gov.bd/ekpaypg/v1',
        'mac_addr' => '1.1.1.1',
        'mer_info' => [
            'mer_reg_id' => 'nise_test',
            'mer_pas_key' => 'NiSe@TsT11'
        ],
        'ipn_info' => [
            'ipn_channel' => '1',
            'ipn_email' => 'noreply@nise.gov.bd',
            'ipn_uri' => 'http://localhost:8001/api/v1/course-enrollment/payment-by-ek-pay/ipn-handler/{secretToken}'
        ]
    ],
    "production" => [
        "ekpay_base_uri" => 'https://pg.ekpay.gov.bd/ekpaypg/v1',
        'mac_addr' => '180.148.214.186',
        'mer_info' => [
            'mer_reg_id' => 'nise_mer',
            'mer_pas_key' => 'NiscE@ekP02'
        ],
        'ipn_info' => [
            'ipn_channel' => '1',
            'ipn_email' => 'noreply@nise.gov.bd',
            'ipn_uri' => 'http://localhost:8001/api/v1/course-enrollment/payment-by-ek-pay/ipn-handler/{secretToken}'
        ]
    ],
    'feed_uri' => [
        'success_uri' => env('EKPAY_SUCCESS_FEED_URI', 'http://localhost:8001/api/v1/payment/success'),
        'fail_uri' => env('EKPAY_FAIL_FEED_URI', 'http://localhost:8001/api/v1/payment/failed'),
        'cancel_uri' => env('EKPAY_CANCEL_FEED_URI', 'http://localhost:8001/api/v1/payment/cancel'),
    ]

];
