<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends BaseModel
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    public const RESULT_PUBLISHED = 1;
    public const RESULT_UNPUBLISHED = 0;


    public const RESULT_PUBLICATIONS = [
        self::RESULT_PUBLISHED,
        self::RESULT_UNPUBLISHED
    ];
}
