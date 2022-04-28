<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\EnrollmentGuardian
 *
 * @property int id
 * @property int course_enrollment_id
 * @property string father_name
 * @property string|null father_name_en
 * @property string|null father_nid
 * @property string|null father_mobile
 * @property Carbon|null father_date_of_birth
 * @property string|null mother_name
 * @property string|null mother_name_en
 * @property string|null mother_nid
 * @property string|null mother_mobile
 * @property Carbon|null mother_date_of_birth
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class EnrollmentGuardian extends BaseModel
{

    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
    protected $table = 'enrollment_guardians';
}
