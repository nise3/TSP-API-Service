<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class RegisteredTrainingOrganization
 * @package App\Models
 * @property int id
 * @property string title_en
 * @property string|null title
 * @property string code
 * @property string|null address_en
 * @property string|null address
 * @property string|null google_map_src
 * @property string|null logo
 * @property string|null config
 * @property string contact_person_name
 * @property string contact_person_mobile
 * @property string contact_person_email
 * @property int|null loc_division_id
 * @property int|null loc_district_id
 * @property int|null loc_upazila_id
 * @property string|null location_latitude
 * @property string|null location_longitude
 * @property string contact_person_designation
 * @property string|null contact_person_designation_en
 * @property int country_id
 * @property string phone_code
 * @property string primary_phone
 */
class RegisteredTrainingOrganization extends BaseModel
{
    public const ROW_STATUSES = [
        self::ROW_STATUS_INACTIVE,
        self::ROW_STATUS_ACTIVE, /** Approved Status */
        self::ROW_STATUS_PENDING,
        self::ROW_STATUS_REJECTED
    ];

    public const RTO_CODE_PREFIX = "RTO";
    public const RTO_CODE_LENGTH = 11;

    use ScopeRowStatusTrait, SoftDeletes;

    /**
     * @var string[]
     */
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    /**
     *
     */
    const DEFAULT_LOGO = 'rto/default.jpg';

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
        return $this->title || $this->title_en;
    }
}
