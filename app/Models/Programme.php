<?php

namespace App\Models;

use App\Traits\scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Programme
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
class Programme extends BaseModel
{
    use ScopeRowStatusTrait, HasFactory;
    use SoftDeletes;

    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class); // TODO: specifically mention columns
    }

    /**
     * @return HasMany
     */
    public function batch(): HasMany
    {
        return $this->hasMany(Batch::class); // TODO: specifically mention columns
    }
}
