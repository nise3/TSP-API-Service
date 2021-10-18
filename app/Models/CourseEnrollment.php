<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Institute
 * @package App\Models
 * @property int id
 * @property int course_id
 * @property int youth_id
 * @property int program_id
 * @property int|null training_center_id
 * @property int|null batch_id
 * @property string first_name
 * @property string|null first_name_en
 * @property string last_name
 * @property string|null last_name_en
 */
class CourseEnrollment extends BaseModel
{
    use ScopeRowStatusTrait,SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
}
