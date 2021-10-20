<?php

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class EnrollmentEducation extends BaseModel
{
    use  SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    /**  CGPA SCALE */
    const GPA_OUT_OF_FIVE = 5;
    const GPA_OUT_OF_FOUR = 4;

    /** Education Attributes Key */
    public const DEGREE = "DEGREE";
    public const BOARD = "BOARD";
    public const MAJOR = "MAJOR";
    public const EXAM_DEGREE_NAME = "EXAM_DEGREE_NAME";
    public const MARKS = "MARKS";
    public const CGPA = "CGPA";
    public const SCALE = "SCALE";
    public const YEAR_OF_PASS = "YEAR_OF_PASS";
    public const EXPECTED_YEAR_OF_PASS = "EXPECTED_YEAR_OF_PASS";
    public const EXPECTED_YEAR_OF_EXPERIENCE = "EXPECTED_YEAR_OF_EXPERIENCE";
    public const EDU_GROUP = "EDU_GROUP";

    /** Trigger Flag For Education Form Validation */
    public const EDUCATION_LEVEL_TRIGGER = "EDUCATION_LEVEL";
    public const RESULT_TRIGGER = "RESULT";
}
