<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\EnrollmentMiscellaneous
 *
 * @property int id
 * @property int course_enrollment_id
 * @property string father_name
 * @property int has_own_family_home
 * @property int has_own_family_land
 * @property int|null number_of_siblings
 * @property int|null recommended_by_any_organization
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class EnrollmentMiscellaneous extends BaseModel
{
    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    protected $table = "enrollment_miscellaneouses";
}
