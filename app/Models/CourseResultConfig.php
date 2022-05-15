<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseResultConfig extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'gradings' => 'array',
        'result_percentages' => 'array'
    ];

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_ONLY_SOFT_DELETE;
}
