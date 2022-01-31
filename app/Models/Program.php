<?php

namespace App\Models;

use App\Traits\Scopes\ScopeAcl;
use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * Class Program
 * @package App\Models
 * @property string title_en
 * @property string title
 * @property int institute_id
 * @property string|null logo
 * @property string code
 * @property string|null description
 * @property int row_status
 * @property-read Batch $batch
 * @property-read Institute $institute
 */
class Program extends BaseModel
{
    use ScopeRowStatusTrait, SoftDeletes;

    /**
     * @var string[]
     */
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    /**
     * @return BelongsTo
     */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class, 'institute_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function batch(): HasMany
    {
        return $this->hasMany(Batch::class, 'institute_id', 'id');
    }

    public function toArray(): array
    {
        $originalData = parent::toArray();
        $authUser = Auth::user();

        if ($authUser && Auth::user()->isIndustryAssociationUser() || !empty($originalData['industry_association_id'])) {
            $this->getIndustryAssociationData($originalData);
        }
        return $originalData;
    }
}
