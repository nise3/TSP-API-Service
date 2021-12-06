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

        if (User::isInstituteUser()) {  //Institute User

            if (User::isTrainingCenterUser()) { //Training Center user
                if (Schema::hasColumn($tableName, 'training_center_id')) {
                    $query = $query->where($tableName . '.training_center_id', $authUser->training_center_id);
                }

            } else if (User::isBranchUser()) { // Branch user
                if (Schema::hasColumn($tableName, 'branch_id')) {
                    $query = $query->where($tableName . '.branch_id', $authUser->branch_id);
                }
            }

            return $query->where($tableName . '.institute_id', $authUser->institute_id);
        }else{ //for public call with param id. Need to optimize
            $instituteId = last(request()->segments());
            if(is_numeric($instituteId)){
                return $query->where($tableName . '.institute_id', $instituteId);
            }
        }
        return $query;
    }
}
