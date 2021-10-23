<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Trainer
 * @package App\Models
 * @property string trainer_name
 * @property string trainer_name_en
 * @property int institute_id
 * @property int|null branch_id
 * @property int|null training_center_id
 * @property string trainer_registration_number
 * @property string code
 * @property string email
 * @property string mobile
 * @property string|null description
 * @property string|null description_en
 * @property string|null about_me
 * @property string|null nationality
 * @property string|null nid
 * @property string|null passport_number
 * @property string|null educational_qualification
 * @property array|null skills
 * @property int gender
 * @property int religion
 * @property int marital_status
 * @property int row_status
 * @property Carbon date_of_birth
 * @property-read Batch $batch
 * @property-read Institute $institute
 */
class Trainer extends BaseModel
{
    use ScopeRowStatusTrait,SoftDeletes;
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    protected $casts = [
        'skills' => 'array',
        'skills_en' => 'array',
    ];
}
