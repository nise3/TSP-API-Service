<?php

namespace App\Traits\Scopes;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait ScopeAcl
{
    /**
     * @param $query
     * @return mixed
     */
    public function scopeAcl($query): mixed
    {
        /** @var User $authUser */
        $authUser = Auth::user();
        $tableName = $this->getTable();

        if ($authUser && $authUser->isInstituteUser()) {  //Institute User

            if ($authUser->isTrainingCenterUser()) { //Training Center user
                if (Schema::hasColumn($tableName, 'training_center_id')) {
                    $query = $query->where($tableName . '.training_center_id', $authUser->training_center_id);
                }

            } else if ($authUser->isBranchUser()) { // Branch user
                if (Schema::hasColumn($tableName, 'branch_id')) {
                    $query = $query->where($tableName . '.branch_id', $authUser->branch_id);
                }
            }

            $query->where($tableName . '.institute_id', $authUser->institute_id);
        } else if ($authUser && $authUser->isIndustryAssociationUser()) { // industry association user
            if (Schema::hasColumn($tableName, 'industry_association_id')) {
                $query = $query->where($tableName . '.industry_association_id', $authUser->industry_association_id);
            }
        } else if ($authUser && $authUser->isRtoUser()) {  // RTO User
            if (Schema::hasColumn($tableName, 'registered_training_organization_id')) {
                $query = $query->where($tableName . '.registered_training_organization_id', $authUser->registered_training_organization_id);
            }
        }

        return $query;
    }
}
