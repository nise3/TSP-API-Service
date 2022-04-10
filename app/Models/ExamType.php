<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamType extends BaseModel
{
    use  SoftDeletes;
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

}
