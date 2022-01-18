<?php

namespace App\Models;

use App\Traits\Scopes\SagaStatusGlobalScope;
use App\Traits\Scopes\ScopeRowStatusTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Institute
 * @package App\Models
 * @property int id
 * @property int course_id
 * @property int youth_id
 * @property int institute_id
 * @property int program_id
 * @property int|null training_center_id
 * @property int|null batch_id
 * @property string first_name
 * @property string|null first_name_en
 * @property string last_name
 * @property string|null last_name_en
 * @property string email
 * @property string mobile
 * @property string | null verification_code
 * @property Carbon | null verification_code_sent_at
 * @property HasOne course
 */
class CourseEnrollment extends BaseModel
{
    use ScopeRowStatusTrait, SoftDeletes;

    public const PAYMENT_STATUS_PAID = 1;
    public const INVOICE_PREFIX = "EN";
    public const INVOICE_SIZE = 38;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;


    public const ROW_STATUSES = [
        self::ROW_STATUS_INACTIVE,
        self::ROW_STATUS_ACTIVE, /** Approved Status */
        self::ROW_STATUS_PENDING,
        self::ROW_STATUS_REJECTED
    ];


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
     * Add @method SagaStatusGlobalScope() as a Global Scope to fetch only saga_status committed data
     */
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SagaStatusGlobalScope);
    }

    /**
     * @return BelongsToMany
     */
    public function physicalDisabilities(): BelongsToMany
    {
        return $this->belongsToMany(PhysicalDisability::class, 'enrollment_physical_disabilities');
    }

    /**
     * @return HasMany
     */
    public function educations(): HasMany
    {
        return $this->hasMany(EnrollmentEducation::class, 'course_enrollment_id')
            ->leftJoin('exam_degrees', 'exam_degrees.id', '=', 'enrollment_educations.exam_degree_id')
            ->leftJoin('edu_groups', 'edu_groups.id', '=', 'enrollment_educations.edu_group_id')
            ->leftJoin('edu_boards', 'edu_boards.id', '=', 'enrollment_educations.edu_board_id')
            ->leftJoin('education_levels', 'education_levels.id', '=', 'enrollment_educations.education_level_id')
            ->select(['enrollment_educations.*',
                'exam_degrees.title as exam_degree_title',
                'exam_degrees.title_en as exam_degree_title_en',
                'edu_groups.title as edu_group_title',
                'edu_groups.title_en as edu_group_title_en',
                'edu_boards.title as edu_board_title',
                'edu_boards.title_en as edu_board_title_en',
                'education_levels.title as education_level_title',
                'education_levels.title_en as education_level_title_en',
            ]);
    }

    /**
     * @return HasMany
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(EnrollmentAddress::class, 'course_enrollment_id')
            ->leftJoin('loc_divisions', 'loc_divisions.id', '=', 'enrollment_addresses.loc_division_id')
            ->leftJoin('loc_districts', 'loc_districts.id', '=', 'enrollment_addresses.loc_district_id')
            ->leftJoin('loc_upazilas', 'loc_upazilas.id', '=', 'enrollment_addresses.loc_upazila_id')
            ->select(['enrollment_addresses.*',
                'loc_divisions.title as loc_division_title',
                'loc_divisions.title_en as loc_division_title_en',
                'loc_districts.title as loc_district_title',
                'loc_districts.title_en as loc_district_title_en',
                'loc_upazilas.title as loc_upazila_title',
                'loc_upazilas.title_en as loc_upazila_tile_en']);
    }

    /**
     * @return HasOne
     */
    public function guardian(): HasOne
    {
        return $this->hasOne(EnrollmentGuardian::class, 'course_enrollment_id');
    }

    /**
     * @return HasOne
     */
    public function miscellaneous(): HasOne
    {
        return $this->hasOne(EnrollmentMiscellaneous::class, 'course_enrollment_id');
    }

}
