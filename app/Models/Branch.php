<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    use ScopeRowStatusTrait;

    protected $guarded = ['id'];

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
