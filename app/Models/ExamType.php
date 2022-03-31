<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamType extends BaseModel
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
    public const EXAM_PURPOSE_COURSE = "COURSE";
    public const EXAM_PURPOSES =[
        self::EXAM_PURPOSE_COURSE
    ];
}
