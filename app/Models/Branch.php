<?php

namespace App\Models;

use App\Traits\Scopes\ScopeFilterByInstitute;
use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Branch
 * @package App\Models
 * @property string title_en
 * @property string title
 * @property int institute_id
 * @property int row_status
 * @property string|null address
 * @property string|null google_map_src
 */
class Branch extends BaseModel
{
    use ScopeRowStatusTrait, SoftDeletes,ScopeFilterByInstitute;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    /**
     *
     * @return BelongsTo
     */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class, 'branch_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function batch(): HasMany
    {
        return $this->hasMany(Batch::class, 'branch_id', 'id');
    }
}
