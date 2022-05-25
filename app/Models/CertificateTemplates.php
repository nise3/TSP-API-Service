<?php

namespace App\Models;

/**
 * Class Program
 * @package App\Models
 * @property string template
 * @property string title_en
 * @property string title
 * @property string result_type
 * @property int accessor_type
 * @property int Accessor_id
 * @property string purpose_name
 * @property int purpose_id
 * @property int issued_at
 * @property string row_status
 * @property string Deleted_at
 */
class CertificateTemplates extends BaseModel
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
}
