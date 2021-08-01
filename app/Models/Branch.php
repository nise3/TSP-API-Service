<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    protected $guarded = ['id'];
    /**
     * @var mixed|string
     */

    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }

    public function courseConfigs(): HasMany
    {
        return $this->hasMany(CourseConfig::class);
    }
}
