<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionBank extends BaseModel
{
    use ScopeRowStatusTrait, SoftDeletes;

    /**
     * @var string[]
     */
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    public const TYPE_MCQ = 1;
    public const TYPE_YES_NO = 2;

    public const ANSWER_OPTION_1 = 1;
    public const ANSWER_OPTION_2 = 2;
    public const ANSWER_OPTION_3 = 3;
    public const ANSWER_OPTION_4 = 4;

    public const ANSWERS = [
        self::ANSWER_OPTION_1,
        self::ANSWER_OPTION_2,
        self::ANSWER_OPTION_3,
        self::ANSWER_OPTION_4,
    ];

    public const TYPES = [
        self::TYPE_MCQ,
        self::TYPE_YES_NO
    ];
}
