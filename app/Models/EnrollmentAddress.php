<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class EnrollmentAddress extends BaseModel
{
    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;


    public const ADDRESS_TYPE_PRESENT = 1;
    public const ADDRESS_TYPE_PERMANENT = 2;
    public const ADDRESS_TYPE_OTHERS = 3;

    public const ADDRESS_TYPES = [
        self::ADDRESS_TYPE_PRESENT,
        self::ADDRESS_TYPE_PERMANENT,
        self::ADDRESS_TYPE_OTHERS,
    ];
}

