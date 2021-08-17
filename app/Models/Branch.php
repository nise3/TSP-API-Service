<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Branch
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 * @property int institute_id
 * @property int row_status
 * @property string|null address
 * @property string|null google_map_src
 * @method static Builder|Institute acl()
 * @method static Builder|Institute active()
 * @method static Builder|Institute newModelQuery()
 * @method static Builder|Institute newQuery()
 * @method static Builder|Institute query()
 */

class Branch extends BaseModel
{
    use SoftDeletes;

    use HasFactory;
    protected $guarded = ['id'];

    /**
     *
     * @return BelongsTo
     */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }

    /**
     * @return HasMany
     */
    public function courseConfigs(): HasMany
    {
        return $this->hasMany(Batche::class);
    }
}
