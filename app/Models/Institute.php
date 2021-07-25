<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Institute
 * @package App\Models
 * @property string title_en
 * @property string|null title_bn
 * @property string code
 * @property string domain
 * @property string|null address
 * @property string|null google_map_src
 * @property string logo
 * @property string|null config
 * @method static Builder|Institute newModelQuery()
 * @method static Builder|Institute newQuery()
 * @method static Builder|Institute query()
 */
class Institute extends BaseModel
{

    use ScopeRowStatusTrait;

    protected $guarded = ['id'];

    const DEFAULT_LOGO = 'institute/default.jpg';

    protected $casts = [
        'phone_numbers' => 'array',
        'mobile_numbers' => 'array',
    ];

    public function title(): ?string
    {
        return $this->title_bn || $this->title_en;
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function programmes(): HasMany
    {
        return $this->hasMany(Programme::class);
    }

    public function trainingCenters(): HasMany
    {
        return $this->hasMany(TrainingCenter::class);
    }

    public function courseConfigs(): HasMany
    {
        return $this->hasMany(CourseConfig::class);
    }
}
