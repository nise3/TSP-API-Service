<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Exam extends BaseModel
{
    use  SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
    public const EXAM_TYPE_ONLINE = 1;
    public const EXAM_TYPE_OFFLINE = 2;
    public const EXAM_TYPE_MIXED = 3;

    public const EXAM_TYPES = [
        self::EXAM_TYPE_ONLINE,
        self::EXAM_TYPE_OFFLINE,
        self::EXAM_TYPE_MIXED,
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
