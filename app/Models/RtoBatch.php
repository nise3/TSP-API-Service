<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class RtoBatch extends BaseModel
{

    use SoftDeletes, ScopeRowStatusTrait;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    const CERTIFICATION_STATUS_NOT_SUBMITTED = 1;
    const CERTIFICATION_STATUS_SUBMITTED = 2;
    const CERTIFICATION_STATUS_CERTIFIED = 3;
    const CERTIFICATION_STATUS_NOT_CERTIFIED = 4;

    public const CERTIFICATION_STATUSES = [
        self::CERTIFICATION_STATUS_NOT_SUBMITTED => "Not submitted",
        self::CERTIFICATION_STATUS_SUBMITTED => "Submitted",
        self::CERTIFICATION_STATUS_CERTIFIED => "Certified",
        self::CERTIFICATION_STATUS_NOT_CERTIFIED => "Not certified",
    ];
}
