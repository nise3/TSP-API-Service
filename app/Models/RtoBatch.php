<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class RtoBatch extends BaseModel
{

    use SoftDeletes, ScopeRowStatusTrait;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    public const CERTIFICATION_STATUSES = [
        "Not submitted" => 1,
        "Submitted" => 2,
        "Certified" => 3,
        "Not certified" => 4
    ];
}
