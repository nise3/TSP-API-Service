<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RplApplicationAddress extends Model
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    public const ADDRESS_TYPE_PRESENT = 1;
    public const ADDRESS_TYPE_PERMANENT = 2;
}
