<?php

namespace App\Models;

use Carbon\Carbon;
use Faker\Provider\Base;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use \Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Auth\Authorizable;

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
 * @property int user_type
 * @property int organization_id
 * @property int institute_id
 * @property int loc_division_id
 * @property int loc_district_id
 * @property int loc_upazila_id
 * @property int branch_id
 * @property int training_center_id
 * @property int industry_association_id
 * @property int registered_training_organization_id
 * @property int $row_status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Role $role
 * @property Collection $permissions
 */
class User extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $guarded = [];
    protected Collection $permissions;
    protected Role $role;

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;


    public function setRole(Role $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function setPermissions(Collection $permissions): static
    {
        $this->permissions = $permissions;
        return $this;
    }

    public function hasPermission($ability): bool
    {
        if (!(!empty($this->permissions) && $this->permissions instanceof Collection)) {
            return false;
        }

        return $this->permissions->contains($ability);
    }

    public function isSystemUser(): bool
    {
        return $this->user_type == BaseModel::SYSTEM_USER_TYPE;
    }

    public function isOrganizationUser(): bool
    {
        return $this->user_type == BaseModel::ORGANIZATION_USER_TYPE && $this->organization_id;
    }

    public function isInstituteUser(): bool
    {
        return $this->user_type == BaseModel::INSTITUTE_USER_TYPE && $this->institute_id;
    }

    public function isIndustryAssociationUser(): bool
    {
        return $this->user_type == BaseModel::INDUSTRY_ASSOCIATION_USER_TYPE && $this->industry_association_id;
    }

    public function isRtoUser(): bool
    {
        return $this->user_type == BaseModel::REGISTERED_TRAINING_ORGANIZATION_USER_TYPE && $this->registered_training_organization_id;
    }

    public function isTrainingCenterUser(): bool
    {
        return $this->isInstituteUser() && $this->training_center_id;
    }

    public function isBranchUser(): bool
    {
        return $this->isInstituteUser() && $this->branch_id && $this->training_center_id == null;
    }


}
