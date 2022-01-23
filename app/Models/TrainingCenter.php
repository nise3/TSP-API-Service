<?php

namespace App\Models;

use App\Traits\Scopes\ScopeAcl;
use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TrainingCenter
 * @package App\Models
 * @property string title_en
 * @property string title
 * @property int institute_id
 * @property int branch_id
 * @property int id
 * @property  int row_status
 * @property string|null address
 * @property string|null google_map_src
 * @property-read Institute $institute
 * @property-read  Branch $branch
 * @property-read  Batch $batch
 */
class TrainingCenter extends BaseModel
{
    use ScopeRowStatusTrait, SoftDeletes, SoftDeletes, ScopeAcl;

    public const CENTER_LOCATION_TYPE_INSTITUTE_PREMISES = 1;
    public const CENTER_LOCATION_TYPE_BRANCH_PREMISES = 2;
    public const CENTER_LOCATION_TYPE_TRAINING_CENTER_PREMISES = 3;
    public const CENTER_LOCATION_TYPES = [
        self::CENTER_LOCATION_TYPE_INSTITUTE_PREMISES,
        self::CENTER_LOCATION_TYPE_BRANCH_PREMISES,
        self::CENTER_LOCATION_TYPE_TRAINING_CENTER_PREMISES,
    ];

    /**
     * @var string[]
     */
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    /**
     * @return BelongsTo
     */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class, 'training_center_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'training_center_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function batch(): HasMany
    {
        return $this->hasMany(Batch::class, 'training_center_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'training_center_skill');
    }
}
