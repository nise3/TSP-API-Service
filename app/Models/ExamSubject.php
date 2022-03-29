<?php

namespace App\Models;

use Faker\Provider\Base;
use Illuminate\Database\Eloquent\Model;

class ExamSubject extends BaseModel
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    public const EXAM_SUBJECT_ASSESSOR_TYPE_INSTITUTE = "INSTITUTE";
    public const EXAM_SUBJECT_ASSESSOR_TYPE_ORGANIZATION = "ORGANIZATION";

    public const EXAM_SUBJECT_ASSESSOR_TYPES = [
        self::EXAM_SUBJECT_ASSESSOR_TYPE_INSTITUTE,
        self::EXAM_SUBJECT_ASSESSOR_TYPE_ORGANIZATION,
    ];
}
