<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LocDivision
 * @package App\Models
 *
 * @property int $id
 * @property string $title_bn
 * @property string|null $title_en
 * @property string|null $bbs_code
 * @property int |null $created_by
 * @property int |null $updated_by
 * @property int $row_status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection|LocUpazila[] $locUpazilas
 * @property-read Collection|LocDistrict[] $locDistricts
 */
class LocDivision extends BaseModel
{
    use ScopeRowStatusTrait, SoftDeletes,HasFactory;

    protected $table = 'loc_divisions';
    protected $guarded = ['id'];


    public function locUpazilas(): HasMany
    {
        return $this->hasMany(LocUpazila::class, 'loc_district_id');
    }

    public function locDistricts(): HasMany
    {
        return $this->hasMany(LocDistrict::class, 'loc_district_id');
    }

}
