<?php

namespace App\Models;

use Carbon\Carbon;

/**
 *Class SSPPessimisticLocking
 * @property int id
 * @property int code
 * @property int last_incremental_value
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class SSPPessimisticLocking extends BaseModel
{
    protected $table = 'ssp_pessimistic_lockings';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'last_incremental_value';
    protected $guarded = [];
    protected $casts = [
        'last_incremental_value' => 'integer'
    ];
}
