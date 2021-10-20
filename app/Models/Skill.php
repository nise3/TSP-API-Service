<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Skill
 * @package App\Models
 * @property string title_en
 * @property string title
 */
class Skill extends BaseModel
{

    use SoftDeletes;

    public $timestamps = false;
    /**
     * @var string[]
     */
    protected $guarded = ['id'];
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
