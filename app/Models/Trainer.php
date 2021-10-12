<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Trainer
 * @package App\Models
 * @property string trainer_name
 * @property string trainer_name_en
 * @property int institute_id
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
    use ScopeRowStatusTrait;
    protected $guarded = ['id'];

    protected $casts = [
        'skills' => 'array',
        'skills_en' => 'array',
    ];
}
