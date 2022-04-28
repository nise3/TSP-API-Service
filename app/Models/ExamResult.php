<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    protected $casts = [
        "answers" => 'array',
        "file_paths" => 'array',
    ];
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE;
}
