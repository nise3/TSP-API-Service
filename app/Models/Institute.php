<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * Class Institute
 * @package App\Models
 * @property int id
 * @property string title_en
 * @property string|null title
 * @property string code
 * @property string domain
 * @property string|null address
 * @property string|null google_map_src
 * @property string logo
 * @property string|null config
 */
class Institute extends BaseModel
{
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
        return $this->hasMany(Branch::class, 'institute_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'institute_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'institute_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function trainingCenters(): HasMany
    {
        return $this->hasMany(TrainingCenter::class, 'institute_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class, 'institute_id', 'id');
    }

    public function trainers(): HasMany
    {
        return $this->hasMany(Trainer::class, 'institute_id', 'id');
    }
}
