<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Skill
 * @package App\Models
 * @property string title_en
 * @property string title
 */
class Skill extends BaseModel
{
    use HasFactory;

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
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_skills');
    }

}
