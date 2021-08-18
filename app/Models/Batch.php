<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Batche
 * @package App\Models
 * @property int institute_id
 * @property int branch_id
 * @property int training_center
 * @property int programme_id
 * @property int course_id
 * @property int row_status
 * @property boolean ethnic
 * @property boolean freedom_fighter
 * @property boolean disable_status
 * @property boolean ssc
 * @property boolean hsc
 * @property boolean honors
 * @property boolean masters
 * @property boolean occupation
 * @property boolean guardian
 * @property-read Institute institute
 * @property-read Branch branch
 * @property-read TrainingCenter trainingCenter
 * @property-read Programme programme
 * @property-read Course course
 */
class Batch extends BaseModel
{
    use ScopeRowStatusTrait, HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return BelongsTo
     */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }

    /**
     * @return BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return BelongsTo
     */
    public function trainingCenter(): BelongsTo
    {
        return $this->belongsTo(TrainingCenter::class);
    }

    /**
     * @return BelongsTo
     */
    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }

}
