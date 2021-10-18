<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class EnrollmentProfessionalInfo extends BaseModel
{
    use SoftDeletes;
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
}
