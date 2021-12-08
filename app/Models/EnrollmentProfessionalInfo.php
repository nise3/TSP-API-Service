<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\EnrollmentProfessionalInfo
 *
 * @property int id
 * @property int course_enrollment_id
 * @property string main_profession
 * @property string|null main_profession_en
 * @property string|null other_profession
 * @property string|null other_profession_en
 * @property double monthly_income
 * @property int is_currently_employed
 * @property int years_of_experiences
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class EnrollmentProfessionalInfo extends BaseModel
{
    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    protected $table = 'enrollment_professional_infos'; //dont remove this
}
