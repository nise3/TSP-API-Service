<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LocUpazila
 * @package App\Models
 *
 * @property int $id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property string $title_bn
 * @property string|null $title_en
 * @property string|null $bbs_code
 * @property string|null $district_bbs_code
 * @property string|null $division_bbs_code
 * @property int $loc_division_id
 * @property int $loc_district_id
 * @property int row_status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read LocDistrict $locDistrict
 * @property-read LocDivision $locDivision
 */
class LocUpazila extends BaseModel
{
    use ScopeRowStatusTrait, SoftDeletes;

    protected $table = 'loc_upazilas';
    protected $guarded = ['id'];

    public function locDistrict(): BelongsTo
    {
        return $this->belongsTo(LocDistrict::class, 'loc_district_id');
    }

    public function locDivision(): BelongsTo
    {
        return $this->belongsTo(LocDivision::class, 'loc_division_id');
    }
}
