<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


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
 * @property string contact_person_name
 * @property string contact_person_mobile
 */
class Institute extends BaseModel
{

    public const ROW_STATUSES = [
        self::ROW_STATUS_INACTIVE,
        self::ROW_STATUS_ACTIVE, /** Approved Status */
        self::ROW_STATUS_PENDING,
        self::ROW_STATUS_REJECTED
    ];

    public const INSTITUTE_CODE_PREFIX = "SSP";
    public const INSTITUTE_CODE_LENGTH = 11;

    use ScopeRowStatusTrait, SoftDeletes;

    /**
     * @var string[]
     */
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

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
    private mixed $contact_person_name;

    /**
     * @return string|null
     */
    public function title(): ?string
    {
        return $this->title || $this->title_en;
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
