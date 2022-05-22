<?php

namespace App\Models;

/**
* Class Program
 * @package App\Models
 * @property int youth_id
 * @property int batch_id
 * @property int certificate_id
 * @property int course_id
 * @property string row_status
*/
class CertificateIssued extends BaseModel
{
    //
    protected $table = 'certificate_issued';
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;
}
