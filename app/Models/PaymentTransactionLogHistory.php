<?php

namespace App\Models;

class PaymentTransactionLogHistory extends BaseModel
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE;

    /** Payment Status */
    public const PAYMENT_SUCCESS = 1;
    public const PAYMENT_PENDING = 2;
    public const PAYMENT_FAIL = 3;
    public const PAYMENT_CANCEL = 4;

    /** Payment Gateway Type */
    public const EK_PAY_LABEL="ek_pay";
    public const SSLCOMMERZ_LABEL="ssl_commerz";
    public const DBBL_MOBILE_BANKING="dbbl_mobile_banking";
    public const BKASH="bkash";
    public const PORT_WALLET="port_wallet";

    public const PAYMENT_GATEWAY_EK_PAY=1;
    public const PAYMENT_GATEWAY_SSLCOMMERZ=2;
    public const PAYMENT_GATEWAY_DBBL_MOBILE_BANKING=3;
    public const PAYMENT_GATEWAY_BKASH=4;
    public const PAYMENT_GATEWAY_PORT_WALLET=5;

    public const PAYMENT_GATEWAYS=[
        self::EK_PAY_LABEL=>self::PAYMENT_GATEWAY_EK_PAY,
        self::SSLCOMMERZ_LABEL=>self::PAYMENT_GATEWAY_SSLCOMMERZ,
        self::DBBL_MOBILE_BANKING=>self::PAYMENT_GATEWAY_DBBL_MOBILE_BANKING,
        self::BKASH=>self::PAYMENT_GATEWAY_BKASH,
        self::PORT_WALLET=>self::PAYMENT_GATEWAY_PORT_WALLET
    ];

    public const PAYMENT_TYPE_COURSE_ENROLLMENT = 1;
    public const TRANSACTION_COMPLETED_SUCCESSFULLY=1020;

    protected $casts=[
        "request_payload"=>'array',
        "response_message"=>"array"
    ];
}
