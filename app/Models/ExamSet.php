<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class ExamSet extends Model
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    public static function examSetId(): string
    {
        $id = Uuid::uuid4();
        $isUnique = !(bool)ExamSet::where('uuid', $id)->count('uuid');
        if ($isUnique) {
            return $id;
        }
        return self::examSetId();

    }
}
