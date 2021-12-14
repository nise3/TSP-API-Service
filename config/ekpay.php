<?php
return [
    "ekpay_base_uri" => env('EK_PAY_BASE_URI', 'https://sandbox.ekpay.gov.bd/ekpaypg/v1'),
    "debug" => env('EK_PAY_DEBUG', false),
    "trnx_currency" => env('TRNX_CURRENCY', 'BDT'),
    'mac_addr' => env('EKPAY_MAC_ADDRESS', '1.1.1.1'),
    "ek_pay_base_config" => [
        'mer_info' => [
            'mer_reg_id' => env('EKPAY_MERCHANT_ID', 'ekMer002'),
            'mer_pas_key' => env('EKPAY_MERCHANT_PASS_KEY', 'p@word1254')
        ],
        'feed_uri' => [
            'success_uri' => env('EKPAY_SUCCESS_FEED_URI', 'www.marchent.com/success/payment?trnsId =00254'),
            'fail_uri' => env('EKPAY_FAIL_FEED_URI', 'www.marchent.com/decline/payment?trnsId =00254'),
            'cancel_uri' => env('EKPAY_CANCEL_FEED_URI', 'www.marchent.com/cancel/payment?trnsId =00254'),
        ],
        'ipn_info' => [
            'ipn_channel' => env('EKPAY_IPN_CHANNEL', '0'),
            'ipn_email' => env('EKPAY_IPN_EMAIL', 'ipn@ekpay.gov.bd'),
            'ipn_uri' => env('EKPAY_IPN_URI', 'www.ekpay.gov.bd/v1/ipn/SendIpn'),
        ]
    ]
];
