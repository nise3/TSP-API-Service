<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 *Class InvoicePessimisticLocking
 * @property int id
 * @property int code
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class InvoicePessimisticLocking extends Model
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE;
}
