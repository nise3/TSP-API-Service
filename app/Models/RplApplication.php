<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class RplApplication extends BaseModel
{
    use SoftDeletes, ScopeRowStatusTrait;

    protected $table = "rpl_applications";
    protected $casts = [
        'youth_details' => 'array'
    ];

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    public const NID = 1;
    public const BIRTH_CARD = 2;
    public const PASSPORT = 3;
    public const IDENTITY_TYPES = [
        self::NID,
        self::BIRTH_CARD,
        self::PASSPORT
    ];


    public const IS_YOUTH_EMPLOYED_TRUE = 1;
    public const IS_YOUTH_EMPLOYED_FALSE = 0;


    public const IS_YOUTH_EMPLOYED = [
        self::IS_YOUTH_EMPLOYED_TRUE,
        self::IS_YOUTH_EMPLOYED_FALSE
    ];


    public const RELIGION_ISLAM = 1;
    public const RELIGION_HINDUISM = 2;
    public const RELIGION_CHRISTIANITY = 3;
    public const RELIGION_BUDDHISM = 4;
    public const RELIGION_JUDAISM = 5;
    public const RELIGION_SIKHISM = 6;
    public const RELIGION_ETHNIC = 7;
    public const RELIGION_AGNOSTIC_ATHEIST = 8;
    public const RELIGIONS = [
        self::RELIGION_ISLAM,
        self::RELIGION_HINDUISM,
        self::RELIGION_CHRISTIANITY,
        self::RELIGION_BUDDHISM,
        self::RELIGION_JUDAISM,
        self::RELIGION_SIKHISM,
        self::RELIGION_ETHNIC,
        self::RELIGION_AGNOSTIC_ATHEIST
    ];

    public const APPLICATION_STATUS_ASSESSMENT_SUBMITTED = 1;
    public const APPLICATION_STATUS_APPLICATION_SUBMITTED = 2;
    public const APPLICATION_STATUS_PAYMENT_COMPLETED = 3;
    public const APPLICATION_STATUS_ASSIGNED_TO_BATCH = 4;
    public const APPLICATION_STATUS_ASSESSMENT_COMPLETED = 5;

    public const RPL_APPLICATION_STATUS = [
        self::APPLICATION_STATUS_ASSESSMENT_SUBMITTED,
        self::APPLICATION_STATUS_APPLICATION_SUBMITTED,
        self::APPLICATION_STATUS_PAYMENT_COMPLETED,
        self::APPLICATION_STATUS_ASSIGNED_TO_BATCH,
        self::APPLICATION_STATUS_ASSESSMENT_COMPLETED,
    ];
}
