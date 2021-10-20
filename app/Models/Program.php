<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    protected $guarded = ['id'];

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
}
