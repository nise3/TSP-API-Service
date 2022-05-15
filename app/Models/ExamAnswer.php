<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    protected $table = "exam_answers";
    protected $casts = [
        "answers" => 'array',
    ];
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE;
}
