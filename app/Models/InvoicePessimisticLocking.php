<?php

namespace App\Models;

use Carbon\Carbon;

/**
 *Class InvoicePessimisticLocking
 * @property int id
 * @property int code
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class InvoicePessimisticLocking extends BaseModel
{
    protected $table = 'invoice_pessimistic_lockings';
    protected $guarded = [];
    protected $casts = [
        'last_incremental_value' => 'integer'
    ];
}
