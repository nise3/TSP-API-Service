<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class ExamSection extends Model
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    public static function examSectionId(): string
    {
        $id = Uuid::uuid4();
        $isUnique = !(bool)ExamSection::where('uuid', $id)->count('uuid');
        if ($isUnique) {
            return $id;
        }
        return self::examSectionId();

    }

}
