<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamQuestionBank extends BaseModel
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    protected $casts = [
        "answer" => 'array',
    ];

    public const EXAM_QUESTION_TYPE_MCQ = 1;
    public const EXAM_QUESTION_TYPE_Fill_IN_THE_BLANKS = 2;
    public const EXAM_QUESTION_TYPE_YES_NO = 3;
    public const EXAM_QUESTION_TYPE_PRACTICAL = 4;
    public const EXAM_QUESTION_TYPE_FIELD_WORK = 5;
    public const EXAM_QUESTION_TYPE_PRESENTATION = 6;
    public const EXAM_QUESTION_TYPE_DESCRIPTIVE = 7;

    public const EXAM_QUESTION_TYPES = [
        self::EXAM_QUESTION_TYPE_MCQ,
        self::EXAM_QUESTION_TYPE_Fill_IN_THE_BLANKS,
        self::EXAM_QUESTION_TYPE_YES_NO,
        self::EXAM_QUESTION_TYPE_PRACTICAL,
        self::EXAM_QUESTION_TYPE_FIELD_WORK,
        self::EXAM_QUESTION_TYPE_PRESENTATION,
        self::EXAM_QUESTION_TYPE_DESCRIPTIVE,
    ];

    public const ANSWER_REQUIRED_QUESTION_TYPE = [
        self::EXAM_QUESTION_TYPE_MCQ,
        self::EXAM_QUESTION_TYPE_Fill_IN_THE_BLANKS,
        self::EXAM_QUESTION_TYPE_YES_NO,
    ];
}
