<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LocDistrict
 * @package App\Models
 *
 * @property int $id
 * @property string $title
 * @property string|null $title_en
 * @property string|null $bbs_code
 * @property int $loc_division_id
 * @property bool|null $is_sadar_district
 * @property-read Collection|LocUpazila[] $locUpazilas
 * @property-read LocDivision $locDivision
 */
class LocDistrict extends BaseModel
{
    use SoftDeletes, HasFactory;

    public $timestamps = false;

    protected $table = 'loc_districts';
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_ONLY_SOFT_DELETE;

    public function locDivision(): BelongsTo
    {
        return $this->belongsTo(LocDivision::class, 'loc_division_id');
    }

    public function locUpazilas(): HasMany
    {
        return $this->hasMany(LocUpazila::class, 'loc_district_id');
    }
}
