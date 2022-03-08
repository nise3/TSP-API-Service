<?php

namespace App\Models;

use App\Traits\Scopes\ScopeAcl;
use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

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
    use ScopeRowStatusTrait, SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    protected $casts = [
        'skills' => 'array',
        'skills_en' => 'array',
    ];

    // TODO: This method should be checked . It gives error.
    /*public function toArray(): array
    {
        $originalData = parent::toArray();
        $authUser = Auth::user();

        if ($authUser && Auth::user()->isIndustryAssociationUser() || !empty($originalData['industry_association_id'])) {
            $this->getIndustryAssociationData($originalData);
        }
        return $originalData;
    }*/

    /**
     * @return BelongsToMany
     */
    public function institutes(): BelongsToMany
    {
        return $this->belongsToMany(Institute::class, 'institute_trainers', 'trainer_id', 'institute_id');
    }

    /**
     * @return BelongsToMany
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'trainer_skills', 'trainer_id', 'skill_id');
    }
}
