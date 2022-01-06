<?php

namespace App\Models;

use Carbon\Carbon;

/**
 * Class PaymentTransactionLogHistory
 * @package App\Models
 *
 * @property int id
 * @property string order_id
 * @property string mer_trnx_id
 * @property string | null trnx_id
 * @property string  type
 * @property int | null  payment_gateway_type
 * @property string | null payment_instrument_type
 * @property string | null payment_instrument_name
 * @property string name
 * @property string mobile
 * @property string email
 * @property numeric amount
 * @property numeric | null paid_amount
 * @property numeric  trnx_currency
 * @property string | null  order_detail
 * @property string | null  request_payload
 * @property string | null  response_message
 * @property string   status
 * @property string | null  ipn_uri_secret_token
 * @property Carbon |null created_at
 * @property Carbon |null updated_at
 *
*/
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
    public const TRANSACTION_COMPLETED_FAIL=1021;
    public const TRANSACTION_COMPLETED_CANCEL=1022;

    protected $casts=[
        "request_payload"=>'array',
        "response_message"=>"array"
    ];
}
