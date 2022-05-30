<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

/**
 * Class Course
 * @package App\Models
 * @property string|null title_en
 * @property string|null title
 * @property string code
 * @property int id
 * @property int institute_id
 * @property int industry_association_id
 * @property int program_id
 * @property double course_fee
 * @property string duration
 * @property string target_group
 * @property string target_group_en
 * @property string contents
 * @property string contents_en
 * @property string training_methodology
 * @property string training_methodology_en
 * @property string evaluation_system
 * @property string evaluation_system_en
 * @property string description
 * @property string description_en
 * @property string objectives
 * @property string objectives_en
 * @property string prerequisite
 * @property string prerequisite_en
 * @property string eligibility
 * @property string eligibility_en
 * @property array application_form_settings
 * @property File cover_image
 * @property-read Program programme
 */
class Course extends BaseModel
{
    use ScopeRowStatusTrait, SoftDeletes, CascadeSoftDeletes;

    protected $table = 'courses';
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    protected $cascadeDeletes = [
        'batches',
        'courseEnrollments',
        'skills'
    ];

    const DEFAULT_COVER_IMAGE = 'course/course.jpeg';

    protected $casts = [
        'application_form_settings' => 'array'
    ];

    public const COURSE_CODE_PREFIX = "C";
    public const COURSE_CODE_SIZE = 19;

    const COURSE_LEVEL_BEGINNER = 1;
    const COURSE_LEVEL_INTERMEDIATE = 2;
    const COURSE_LEVEL_EXPERT = 3;

    const COURSE_LEVELS = [
        self::COURSE_LEVEL_BEGINNER,
        self::COURSE_LEVEL_INTERMEDIATE,
        self::COURSE_LEVEL_EXPERT
    ];

    const COURSE_LANGUAGE_MEDIUM_BENGALI = 1;
    const COURSE_LANGUAGE_MEDIUM_ENGLISH = 2;

    const COURSE_LANGUAGE_MEDIUMS = [
        self::COURSE_LANGUAGE_MEDIUM_BENGALI,
        self::COURSE_LANGUAGE_MEDIUM_ENGLISH,
    ];

    /** Course filter parameters */
    const COURSE_FILTER_TYPE_RECENT = 'recent';
    const COURSE_FILTER_TYPE_POPULAR = 'popular';
    const COURSE_FILTER_TYPE_NEARBY = 'nearby';
    const COURSE_FILTER_TYPE_SKILL_MATCHING = 'skill-matching';
    const COURSE_FILTER_TYPE_TRENDING = 'trending';
    const COURSE_FILTER_TYPES = [
        self::COURSE_FILTER_TYPE_RECENT,
        self::COURSE_FILTER_TYPE_POPULAR,
        self::COURSE_FILTER_TYPE_NEARBY,
        self::COURSE_FILTER_TYPE_SKILL_MATCHING,
        self::COURSE_FILTER_TYPE_TRENDING
    ];

    const COURSE_FILTER_AVAILABILITY_RUNNING = 1;
    const COURSE_FILTER_AVAILABILITY_UPCOMING = 2;
    const COURSE_FILTER_AVAILABILITY_COMPLETED = 3;
    const COURSE_FILTER_AVAILABILITIES = [
        self::COURSE_FILTER_AVAILABILITY_RUNNING,
        self::COURSE_FILTER_AVAILABILITY_UPCOMING,
        self::COURSE_FILTER_AVAILABILITY_COMPLETED,
    ];

    const COURSE_FILTER_COURSE_TYPE_PAID = 1;
    const COURSE_FILTER_COURSE_TYPE_FREE = 2;
    const COURSE_FILTER_COURSE_TYPES = [
        self::COURSE_FILTER_COURSE_TYPE_PAID,
        self::COURSE_FILTER_COURSE_TYPE_FREE
    ];

    /**
     * @return BelongsTo
     */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class, 'course_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class, 'course_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function certificateIssues(): HasMany
    {
        return $this->hasMany(CertificateIssued::class,'course_id','id');
    }

    /**
     * @return BelongsTo
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'course_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'course_skill');
    }

    public function courseEnrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class, 'course_id');
    }

}
