<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LocDistrict
 * @package App\Models
 *
 * @property int $id
 * @property string $title_bn
 * @property string|null $title_en
 * @property string|null $bbs_code
 * @property string|null $division_bbs_code
 * @property int $loc_division_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property bool|null $is_sadar_district
 * @property-read Collection|LocUpazila[] $locUpazilas
 * @property-read LocDivision $locDivision
 * @property int |null $created_by
 * @property int |null $updated_by
 */
class LocDistrict extends BaseModel
{
    use ScopeRowStatusTrait, SoftDeletes;

    protected $table = 'loc_districts';
    protected $guarded = ['id'];

    public function locDivision(): BelongsTo
    {
        return $this->belongsTo(LocDivision::class, 'loc_division_id');
    }

    public function locUpazilas(): HasMany
    {
        return $this->hasMany(LocUpazila::class, 'loc_district_id');
    }
}
