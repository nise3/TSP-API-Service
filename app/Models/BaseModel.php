<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 * @package App\Models
 */
abstract class BaseModel extends Model
{
    use ScopeRowStatusTrait;
    public const ROW_STATUS_ACTIVE = '1';
    public const ROW_STATUS_INACTIVE = '0';
    public const ROW_ORDER_ASC='ASC';
    public const ROW_ORDER_DESC='DESC';

    public const ETHNIC_GROUP_INFO='ethnic_group_info';
    public const FREEDOM_FIGHTER_INFO='freedom_fighter_info';
    public const DISABILITY_INFO='disability_info';
    public const SSC_PASSING_INFO='ssc_passing_info';
    public const HSC_PASSING_INFO='hsc_passing_status';
    public const HONOURS_PASSING_INFO='honors_passing_info';
    public const MASTERS_PASSING_INFO='masters_passing_info';
    public const OCCUPATION_INFO='occupation_info';
    public const GUARDIAN_INFO='guardian_info';

    /** Institute User Type*/
    public const INSTITUTE_USER = 3;

    /** Client Url End Point Type*/
    public const CORE_CLIENT_URL_TYPE = "CORE";

    public const DYNAMIC_FORM_FIELD_INFO=[
        self::ETHNIC_GROUP_INFO=>[
            true,
            false
        ],
        self::FREEDOM_FIGHTER_INFO=>[
            true,
            false
        ],
        self::DISABILITY_INFO=>[
            true,
            false
        ],
        self::SSC_PASSING_INFO=>[
            true,
            false
        ],
        self::HSC_PASSING_INFO=>[
            true,
            false
        ],
        self::HONOURS_PASSING_INFO=>[
            true,
            false
        ],
        self::MASTERS_PASSING_INFO=>[
            true,
            false
        ],
        self::OCCUPATION_INFO=>[
            true,
            false
        ],
        self::GUARDIAN_INFO=>[
            true,
            false
        ]

    ];

    public const MOBILE_REGEX= 'regex: /^(01[3-9]\d{8})$/';

}
