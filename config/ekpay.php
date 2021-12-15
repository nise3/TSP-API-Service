<?php
return [
    "ekpay_base_uri" => env('EK_PAY_BASE_URI', 'https://sandbox.ekpay.gov.bd/ekpaypg/v1'),
    "debug" => env('EK_PAY_DEBUG', false),
    "trnx_currency" => env('TRNX_CURRENCY', 'BDT'),
    'mac_addr' => env('EKPAY_MAC_ADDRESS', '1.1.1.1'),
    "ek_pay_base_config" => [
        'mer_info' => [
            'mer_reg_id' => env('EKPAY_MERCHANT_ID', 'nise_test'),
            'mer_pas_key' => env('EKPAY_MERCHANT_PASS_KEY', 'NiSe@TsT11')
        ],
        'feed_uri' => [
            'success_uri' => env('EKPAY_SUCCESS_FEED_URI', 'api/v1/payment/success'),
            'fail_uri' => env('EKPAY_FAIL_FEED_URI', 'api/v1/payment/fail'),
            'cancel_uri' => env('EKPAY_CANCEL_FEED_URI', 'api/v1/payment/cancel'),
        ],
        'ipn_info' => [
            'ipn_channel' => env('EKPAY_IPN_CHANNEL', '0'),
            'ipn_email' => env('EKPAY_IPN_EMAIL', 'tasmidur.softbdltd@gmail.com'),
            'ipn_uri' => env('EKPAY_IPN_URI', 'api/v1/payment/ipn-handler'),
        ]
    ]
];
