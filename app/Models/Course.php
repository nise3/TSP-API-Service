<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;

/**
 * Class Course
 * @package App\Models
 * @property string|null title_en
 * @property string|null title_bn
 * @property string code
 * @property int institute_id
 * @property double course_fee
 * @property string duration
 * @property string target_group
 * @property string contents
 * @property string training_methodology
 * @property string evaluation_system
 * @property string description
 * @property string objectives
 * @property string prerequisite
 * @property string eligibility
 * @property File cover_image
 * @method static Builder|Institute active()
 * @method static Builder|Institute newModelQuery()
 * @method static Builder|Institute newQuery()
 * @method static Builder|Institute query()
 */

class Course extends BaseModel
{
    use ScopeRowStatusTrait, HasFactory;

    use SoftDeletes;
    protected $table = 'courses';
    protected $guarded = ['id'];

    const DEFAULT_COVER_IMAGE = 'course/course.jpeg';

    /**
     * @return BelongsTo
     */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }

    /**
     * @return HasMany
     */
    public function courseSessions(): HasMany
    {
        return $this->hasMany(CourseSession::class,'course_id','id');
    }

    /**
     * @return HasMany
     */
    public function courseConfigs(): HasMany
    {
        return $this->hasMany(CourseConfig::class);
    }
}
