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
 * @property string description
 * @property int row_status
 * @property-read courseConfig $courseConfig
 * @property-read Institute $institute
 */
class Programme extends BaseModel
{
    use ScopeRowStatusTrait;

    protected $guarded = ['id'];

    const DEFAULT_LOGO = 'programme/default.jpg';

    /**
     * @return BelongsTo
     */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }

    public function courseConfig(): HasMany
    {
        return $this->hasMany(CourseConfig::class);
    }

    /**
     * @return bool
     */
    public function logoIsDefault(): bool
    {
        return $this->logo === self::DEFAULT_LOGO;
    }
}
