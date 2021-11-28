<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 * @package App\Models
 */
abstract class BaseModel extends Model
{
    use HasFactory;

    public const COMMON_GUARDED_FIELDS_ONLY_SOFT_DELETE = ['id', 'deleted_at'];
    public const COMMON_GUARDED_FIELDS_SIMPLE = ['id', 'created_at', 'updated_at'];
    public const COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE = ['id', 'created_at', 'updated_at', 'deleted_at'];
    public const COMMON_GUARDED_FIELDS_SOFT_DELETE = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];
    public const COMMON_GUARDED_FIELDS_NON_SOFT_DELETE = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    public const ROW_STATUS_INACTIVE = 0;
    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_PENDING = 2;
    public const ROW_STATUS_REJECTED = 3;


    public const ROW_ORDER_ASC = 'ASC';
    public const ROW_ORDER_DESC = 'DESC';

    public const INSTITUTE_TYPE_GOVT = 1;
    public const INSTITUTE_TYPE_NON_GOVT = 2;
    public const INSTITUTE_TYPE_OTHERS_ = 3;


    public const TRUE = 1;
    public const FALSE = 0;

    /** Religions Mapping  */
    public const RELIGION_ISLAM = 1;
    public const RELIGION_HINDUISM = 2;
    public const RELIGION_CHRISTIANITY = 3;
    public const RELIGION_BUDDHISM = 4;
    public const RELIGION_JUDAISM = 5;
    public const RELIGION_SIKHISM = 6;
    public const RELIGION_ETHNIC = 7;
    public const RELIGION_AGNOSTIC_ATHEIST = 8;

    /** Youth Identity Type */
    public const IDENTITY_TYPE_NID = 1;
    public const IDENTITY_TYPE_BCERT = 2;
    public const IDENTITY_TYPE_PASSPORT = 3;

    /** Marital Statuses */
    public const MARITAL_STATUS_SINGLE = 1;
    public const MARITAL_STATUS_MARRIED = 2;
    public const MARITAL_STATUS_WIDOWED = 3;
    public const MARITAL_STATUS_DIVORCED = 4;

    /** Gender Statuses */
    public const MALE = 1;
    public const FEMALE = 2;
    public const OTHERS = 3;
    public const GENDERS = [
        self::MALE,
        self::FEMALE,
        self::OTHERS
    ];

    public const ETHNIC_GROUP_INFO = 'ethnic_group_info';
    public const FREEDOM_FIGHTER_INFO = 'freedom_fighter_info';
    public const DISABILITY_INFO = 'disability_info';
    public const SSC_PASSING_INFO = 'ssc_passing_info';
    public const HSC_PASSING_INFO = 'hsc_passing_status';
    public const HONOURS_PASSING_INFO = 'honors_passing_info';
    public const MASTERS_PASSING_INFO = 'masters_passing_info';
    public const OCCUPATION_INFO = 'occupation_info';
    public const GUARDIAN_INFO = 'guardian_info';

    /** Institute User Type*/
    public const INSTITUTE_USER_TYPE = 3;
    public const DEFAULT_PAGE_SIZE = 10;

    /** Client Url End Point Type*/
    public const ORGANIZATION_CLIENT_URL_TYPE = "ORGANIZATION";
    public const INSTITUTE_URL_CLIENT_TYPE = "INSTITUTE";
    public const CORE_CLIENT_URL_TYPE = "CORE";
    public const YOUTH_CLIENT_URL_TYPE = "YOUTH";
    public const IDP_SERVER_CLIENT_URL_TYPE = "IDP_SERVER";
    public const CMS_CLIENT_URL_TYPE = "CMS";
    public const MAIL_SMS_SEND = "MAIL_SMS_SEND";

    public const MOBILE_REGEX = 'regex: /^(01[3-9]\d{8})$/';
    const INSTITUTE_USER_REGISTRATION_ENDPOINT_LOCAL = '';

    public const PASSWORD_MIN_LENGTH = 8;
    public const PASSWORD_MAX_LENGTH = 50;

    public const PASSWORD_REGEX = 'regex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/';
    public const PASSWORD_VALIDATION_MESSAGE = 'The password must contain at least one uppercase, lowercase letter and at least one number.[66000]';

    public const NISE3_FROM_EMAIL = "info@nise3.com";

}
