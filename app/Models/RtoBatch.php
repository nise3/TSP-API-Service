<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class RtoBatch extends BaseModel
{
    use SoftDeletes;
    public const CERTIFICATION_STATUSES = [
        "Not submitted" => 1,
        "Submitted" => 2,
        "Certified" => 3,
        "Not certified" => 4
    ];
}
