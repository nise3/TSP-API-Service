<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamType extends BaseModel
{
    use  SoftDeletes, CreatedUpdatedBy;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
    public const EXAM_PURPOSE_BATCH = "BATCH";
    public const EXAM_PURPOSES = [
        self::EXAM_PURPOSE_BATCH
    ];

    /** purpose name tables */
    public const EXAM_PURPOSE_TABLE_BATCH = 'batches';


    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'exam_type_id', 'id');
    }

    public function batches(): BelongsToMany
    {
        return $this->belongsToMany(Batch::class, 'batch_exams', 'exam_type_id', 'batch_id');
    }

}
