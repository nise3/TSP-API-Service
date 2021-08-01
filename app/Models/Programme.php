<?php

namespace App\Models;

use App\Traits\scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Programme
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 * @property int institute_id
 * @property string|null logo
 * @property string code
 * @property string|null description
 * @property int row_status
 * @property-read CourseConfig $courseConfig
 * @property-read Institute $institute
 */
class Programme extends BaseModel
{
    use ScopeRowStatusTrait;

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
     * @return HasMany
     */
    public function courseConfig(): HasMany
    {
        return $this->hasMany(CourseConfig::class);
    }
}
