<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SagaErrorEvent
 * @package App\Models
 * @property int id
 * @property string uuid
 * @property string connection
 * @property string publisher
 * @property string listener
 * @property string exchange
 * @property string routing_key
 * @property string consumer
 * @property string event_data
 * @property string error_message
 * @property Carbon |null created_at
 * @property Carbon |null updated_at
 *
*/
class SagaErrorEvent extends BaseModel
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE;
}
