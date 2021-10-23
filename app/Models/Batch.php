<?php

namespace App\Models;


use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Batche
 * @package App\Models
 * @property int institute_id
 * @property int branch_id
 * @property int training_center_id
 * @property int course_id
 * @property int row_status
 * @property int number_of_seats
 * @property int available_seats
 * @property Carbon registration_start_date
 * @property Carbon registration_end_date
 * @property Carbon batch_start_date
 * @property Carbon batch_end_date
 * @property-read Institute institute
 * @property-read Branch branch
 * @property-read TrainingCenter trainingCenter
 * @property-read Course course
 */
class Batch extends BaseModel
{

    use ScopeRowStatusTrait,SoftDeletes;
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    /**
     * @return BelongsTo
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'batch_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class, 'batch_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'batch_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function trainingCenter(): BelongsTo
    {
        return $this->belongsTo(TrainingCenter::class, 'batch_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function trainers(): BelongsToMany
    {
        return $this->belongsToMany(Trainer::class, 'trainer_batch', 'batch_id', 'trainer_id');
    }

}
