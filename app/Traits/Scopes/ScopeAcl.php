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
    public function ScopeAcl($query): mixed
    {

        $authUser = Auth::user();
        $tableName = $this->getTable();

        if ($authUser && $authUser->isInstituteUser()) {  //Institute User

            if ($authUser && $authUser->isTrainingCenterUser()) { //Training Center user
                if (Schema::hasColumn($tableName, 'training_center_id')) {
                    $query = $query->where($tableName . '.training_center_id', $authUser->training_center_id);
                }

            } else if ($authUser && $authUser->isBranchUser()) { // Branch user
                if (Schema::hasColumn($tableName, 'branch_id')) {
                    $query = $query->where($tableName . '.branch_id', $authUser->branch_id);
                }
            }

            return $query->where($tableName . '.institute_id', $authUser->institute_id);
        }
        return $query;
    }
}
