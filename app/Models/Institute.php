<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


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
 */
class Institute extends BaseModel
{

    use ScopeRowStatusTrait, HasFactory;
    use SoftDeletes;

    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     *
     */
    const DEFAULT_LOGO = 'institute/default.jpg';

    /**
     * @var string[]
     */
    protected $casts = [
        'phone_numbers' => 'array',
        'mobile_numbers' => 'array',
    ];

    /**
     * @return string|null
     */
    public function title(): ?string
    {
        return $this->title_bn || $this->title_en;
    }

    /**
     * @return HasMany
     */
    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * @return HasMany
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * @return HasMany
     */
    public function programmes(): HasMany
    {
        return $this->hasMany(Programme::class);
    }

    /**
     * @return HasMany
     */
    public function trainingCenters(): HasMany
    {
        return $this->hasMany(TrainingCenter::class);
    }

    /**
     * @return HasMany
     */
    public function courseConfigs(): HasMany
    {
        return $this->hasMany(CourseConfig::class);
    }
}
