<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamType extends BaseModel
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    public const EXAM_TYPE_ONLINE = 1;
    public const EXAM_TYPE_OFFLINE = 2;
    public const EXAM_TYPE_MIXED = 3;

    public const EXAM_TYPES = [
        self::EXAM_TYPE_ONLINE,
        self::EXAM_TYPE_OFFLINE,
        self::EXAM_TYPE_MIXED,
    ];

    public const EXAM_PURPOSE_COURSE = "COURSE";
    public const EXAM_PURPOSE_COURSES =[
        self::EXAM_PURPOSE_COURSE
    ];
}
