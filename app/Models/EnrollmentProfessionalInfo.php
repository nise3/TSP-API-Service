<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;

class EnrollmentProfessionalInfo extends BaseModel
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
}
