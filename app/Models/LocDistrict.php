<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class LocDistrict
 * @package App\Models
 *
 * @property int $id
 * @property string $title
 * @property string|null $title_en
 * @property string|null $bbs_code
 * @property int $loc_division_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property bool|null $is_sadar_district
 * @property-read Collection|LocUpazila[] $locUpazilas
 * @property-read LocDivision $locDivision
 */
class LocDistrict extends BaseModel
{
    protected $table = 'loc_districts';
    public $timestamps = false;
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    public function locDivision(): BelongsTo
    {
        return $this->belongsTo(LocDivision::class, 'loc_division_id');
    }

    public function locUpazilas(): HasMany
    {
        return $this->hasMany(LocUpazila::class, 'loc_district_id');
    }
}
