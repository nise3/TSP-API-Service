<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;

/**
 * Class Course
 * @package App\Models
 * @property string|null title_en
 * @property string|null title
 * @property string code
 * @property int institute_id
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
    use ScopeRowStatusTrait, SoftDeletes;

    protected $table = 'courses';
    protected $guarded = ['id'];

    const DEFAULT_COVER_IMAGE = 'course/course.jpeg';

    protected $casts = [
        'application_form_settings' => 'array'
    ];

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
}
