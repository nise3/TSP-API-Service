<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 *Class MerchantCodePessimisticLocking
 * @property int id
 * @property int code
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class MerchantCodePessimisticLocking extends Model
{
    protected $table = 'merchant_code_pessimistic_lockings';
    protected $guarded = [];
    protected $casts = [
        'last_incremental_value' => 'integer'
    ];
}
