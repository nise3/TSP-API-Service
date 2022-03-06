<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentQuestion extends BaseModel
{
    use ScopeRowStatusTrait;

    /**
     * @var string[]
     */
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE;
    public const TYPE_MCQ =  1;
    public const TYPE_YES_NO =  2;

    public const TYPES = [
        self::TYPE_MCQ,
        self::TYPE_YES_NO
    ];
}
