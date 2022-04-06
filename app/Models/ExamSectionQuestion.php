<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class ExamSectionQuestion extends Model
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    protected $casts = [
        "answers" => 'array',
    ];

    public static function examSectionQuestionId(): string
    {
        $id = Uuid::uuid4();
        $isUnique = !(bool)ExamSectionQuestion::where('uuid', $id)->count('uuid');
        if ($isUnique) {
            return $id;
        }
        return self::examSectionQuestionId();

    }
}
