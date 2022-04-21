<?php

namespace App\Models;

/**
 * Class Program
 * @package App\Models
 * @property string certificate_id
 * @property string youth_id
 * @property string batch_id
 * @property string row_status
 */
class Certificate_Issued extends BaseModel
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;
}
