<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class EducationLevel extends BaseModel
{
    /** Education Level Status Code */
    public const EDUCATION_LEVEL_PSC_5_PASS = "PSC_5_PASS";
    public const EDUCATION_LEVEL_JSC_JDC_8_PASS = "JSC_JDC_8_PASS";
    public const EDUCATION_LEVEL_SECONDARY = "SECONDARY";
    public const EDUCATION_LEVEL_HIGHER_SECONDARY = "HIGHER_SECONDARY";
    public const EDUCATION_LEVEL_DIPLOMA = "DIPLOMA";
    public const EDUCATION_LEVEL_BACHELOR = "BACHELOR";
    public const EDUCATION_LEVEL_MASTERS = "MASTERS";
    public const EDUCATION_LEVEL_PHD = "PHD";

    public const EDUCATION_LEVELS = [
        self::EDUCATION_LEVEL_PSC_5_PASS,
        self::EDUCATION_LEVEL_JSC_JDC_8_PASS,
        self::EDUCATION_LEVEL_SECONDARY,
        self::EDUCATION_LEVEL_HIGHER_SECONDARY,
        self::EDUCATION_LEVEL_DIPLOMA,
        self::EDUCATION_LEVEL_BACHELOR,
        self::EDUCATION_LEVEL_MASTERS,
        self::EDUCATION_LEVEL_PHD,
    ];

    /** Result Level Status Code */
    public const RESULT_FIRST_DIVISION = "FIRST_DIVISION";
    public const RESULT_SECOND_DIVISION = "SECOND_DIVISION";
    public const RESULT_THIRD_DIVISION = "THIRD_DIVISION";
    public const RESULT_GRADE = "GRADE";
    public const RESULT_APPEARED = "APPEARED";
    public const RESULT_AWARDED = "AWARDED";
    public const RESULT_ENROLLED = "ENROLLED";
    public const RESULT_DO_NOT_MENTION = "DO_NOT_MENTION";
    public const RESULT_PASS = "PASS";

    public const RESULTS = [
        self::RESULT_FIRST_DIVISION,
        self::RESULT_SECOND_DIVISION,
        self::RESULT_THIRD_DIVISION,
        self::RESULT_GRADE,
        self::RESULT_APPEARED,
        self::RESULT_AWARDED,
        self::RESULT_ENROLLED,
        self::RESULT_DO_NOT_MENTION,
        self::RESULT_PASS,
    ];

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    public function examDegrees(): HasMany
    {
        return $this->hasMany(ExamDegree::class, "education_level_id");
    }
}
