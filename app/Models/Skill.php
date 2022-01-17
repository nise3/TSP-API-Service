<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Skill
 * @package App\Models
 * @property string title_en
 * @property string title
 * @property Carbon |null created_at
 * @property Carbon |null updated_at
 * @property Carbon |null deleted_at
 */
class Skill extends BaseModel
{

    use SoftDeletes;

    public $timestamps = false;
    /**
     * @var string[]
     */
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_ONLY_SOFT_DELETE;
    /**
     * @var string[]
     */
    protected $hidden = ["pivot"];

    /**
     * @return BelongsToMany
     */
    public function trainingCenters(): BelongsToMany
    {
        return $this->belongsToMany(TrainingCenter::class, 'training_center_skill');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_skill');
    }
}
