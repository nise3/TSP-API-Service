<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YouthExam extends BaseModel
{
    protected $casts = [
        "file_paths" => 'array',
    ];
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE;
}
