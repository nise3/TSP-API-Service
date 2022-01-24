<?php

namespace App\Models;

use Carbon\Carbon;

/**
 *Class SSPPessimisticLocking
 * @property int id
 * @property int code
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class SSPPessimisticLocking extends BaseModel
{
    protected $table = 'ssp_pessimistic_lockings';
    protected $guarded = [];
    protected $casts = [
        'last_incremental_value' => 'integer'
    ];
}
