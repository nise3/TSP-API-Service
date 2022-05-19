<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;


class Exam extends BaseModel
{
    use  SoftDeletes, CreatedUpdatedBy;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    public const EXAM_TYPE_ONLINE = 1;
    public const EXAM_TYPE_OFFLINE = 2;
    public const EXAM_TYPE_MIXED = 3;
    public const EXAM_TYPE_PRACTICAL = 4;
    public const EXAM_TYPE_FIELD_WORK = 5;
    public const EXAM_TYPE_PRESENTATION = 6;
    public const EXAM_TYPE_ASSIGNMENT = 7;
    public const EXAM_TYPE_ATTENDANCE = 8;

    public const EXAM_TYPES = [
        self::EXAM_TYPE_ONLINE,
        self::EXAM_TYPE_OFFLINE,
        self::EXAM_TYPE_MIXED,
        self::EXAM_TYPE_PRACTICAL,
        self::EXAM_TYPE_FIELD_WORK,
        self::EXAM_TYPE_PRESENTATION,
        self::EXAM_TYPE_ASSIGNMENT,
    ];

    public const YOUTH_EXAM_TYPES = [
        self::EXAM_TYPE_ONLINE,
        self::EXAM_TYPE_OFFLINE,
        self::EXAM_TYPE_PRACTICAL,
        self::EXAM_TYPE_FIELD_WORK,
        self::EXAM_TYPE_PRESENTATION,
        self::EXAM_TYPE_ASSIGNMENT,
        self::EXAM_TYPE_ATTENDANCE,
    ];

    public const DURATION_REQUIRED_EXAM_TYPES = [
        self::EXAM_TYPE_ONLINE,
        self::EXAM_TYPE_OFFLINE,
        self::EXAM_TYPE_MIXED,
    ];

    public const EXAM_TYPES_WITHOUT_QUESTION = [
        self::EXAM_TYPE_PRACTICAL,
        self::EXAM_TYPE_FIELD_WORK,
        self::EXAM_TYPE_PRESENTATION,
        self::EXAM_TYPE_ASSIGNMENT,
    ];

    public const EXAM_PUBLISHED = 1;
    public const EXAM_UNPUBLISHED = 0;


    public const EXAM_PUBLICATION = [
        self::EXAM_PUBLISHED,
        self::EXAM_UNPUBLISHED
    ];

    public function examSections(): HasMany
    {
        return $this->hasMany(ExamSection::class, 'exam_id', 'id');
    }

    public function examSets(): HasMany
    {
        return $this->hasMany(ExamSet::class, 'exam_id', 'id');
    }
}
