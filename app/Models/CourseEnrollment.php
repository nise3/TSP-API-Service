<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Institute
 * @package App\Models
 * @property int id
 * @property int course_id
 * @property int youth_id
 * @property int program_id
 * @property int|null training_center_id
 * @property int|null batch_id
 * @property string first_name
 * @property string|null first_name_en
 * @property string last_name
 * @property string|null last_name_en
 */
class CourseEnrollment extends BaseModel
{
    use ScopeRowStatusTrait,SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    /** Marital Statuses */
    public const MARITAL_STATUS_SINGLE = 1;
    public const MARITAL_STATUS_MARRIED = 2;
    public const MARITAL_STATUS_WIDOWED = 3;
    public const MARITAL_STATUS_DIVORCED = 4;
    public const MARITAL_STATUSES = [
        self::MARITAL_STATUS_SINGLE,
        self::MARITAL_STATUS_MARRIED,
        self::MARITAL_STATUS_WIDOWED,
        self::MARITAL_STATUS_DIVORCED
    ];


    /** Religions Mapping  */
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

    /**  Identity Number Type  */
    public const NID = 1;
    public const BIRTH_CARD = 2;
    public const PASSPORT = 3;
    public const IDENTITY_TYPES = [
        self::NID,
        self::BIRTH_CARD,
        self::PASSPORT
    ];

    /** Freedom fighter statuses */
    public const NON_FREEDOM_FIGHTER = 1;
    public const FREEDOM_FIGHTER = 2;
    public const CHILD_OF_FREEDOM_FIGHTER = 3;
    public const GRAND_CHILD_OF_FREEDOM_FIGHTER = 4;
    public const FREEDOM_FIGHTER_STATUSES = [
        self::NON_FREEDOM_FIGHTER,
        self::FREEDOM_FIGHTER,
        self::CHILD_OF_FREEDOM_FIGHTER,
        self::GRAND_CHILD_OF_FREEDOM_FIGHTER
    ];

    /**
     * @return BelongsToMany
     */
    public function physicalDisabilities(): BelongsToMany
    {
        return $this->belongsToMany(PhysicalDisability::class, 'enrollment_physical_disabilities');
    }
}
