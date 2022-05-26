<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocCityCorporationWard extends BaseModel
{
    use SoftDeletes, HasFactory;

    protected $table = 'loc_city_corporation_wards';
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_ONLY_SOFT_DELETE;
    public $timestamps = false;

    public function locDistrict(): BelongsTo
    {
        return $this->belongsTo(LocDistrict::class, 'loc_district_id');
    }

    public function locDivision(): BelongsTo
    {
        return $this->belongsTo(LocDivision::class, 'loc_division_id');
    }

    public function locCityCorporation(): BelongsTo
    {
        return $this->belongsTo(LocCityCorporation::class, 'loc_city_corporation_id');
    }
}
