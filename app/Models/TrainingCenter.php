<?php

namespace App\Models;

use App\Traits\scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TrainingCenter
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 * @property int institute_id
 * @property int branch_id
 * @property  int row_status
 * @property string|null address
 * @property string|null google_map_src
 * @property-read Institute $institute
 * @property-read  Branch $branch
 * @property-read  Batch $batch
 */
class TrainingCenter extends BaseModel
{
    use  ScopeRowStatusTrait, HasFactory;
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
        return $this->belongsTo(Institute::class);
    }

    /**
     * @return BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return HasMany
     */
    public function batch(): HasMany
    {
        return $this->hasMany(Batch::class);
    }
}
