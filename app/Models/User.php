<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class User
 * @package App\Models
 * @property string name_en
 * @property string username
 * @property string name_bn
 * @property string email
 * @property string mobile
 * @property string profile_pic
 * @property int $role_id
 * @property int $user_type
 * @property int organization_id
 * @property int institute_id
 * @property int loc_division_id
 * @property int loc_district_id
 * @property int loc_upazila_id
 * @property int $row_status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Role $role
 * @property Collection $permissions
 */
class User extends BaseModel
{
    protected $guarded = [];

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;

    public function hasPermission($key): bool
    {
        return $this->permissions->contains($key);
    }

}
