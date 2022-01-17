<?php

namespace App\Models;


use Carbon\Carbon;

/**
 * Class SagaSuccessEvents
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
class SagaSuccessEvent extends BaseModel
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE;
}
