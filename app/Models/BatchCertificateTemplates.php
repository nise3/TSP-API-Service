<?php

namespace App\Models;

/**
 * Class Program
 * @package App\Models
 * @property int certificate_template_id
 * @property int batch_id
 */
class BatchCertificateTemplates extends BaseModel
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
}
