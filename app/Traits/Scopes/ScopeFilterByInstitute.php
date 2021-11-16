<?php

namespace App\Traits\Scopes;

use App\Models\BaseModel;
use Illuminate\Support\Facades\Auth;

trait ScopeFilterByInstitute
{
    /**
     * @param $query
     * @return mixed
     */
    public function scopeByInstitute($query): mixed
    {
        $authUser = Auth::user();
        $tableName = $this->getTable();
        if ($authUser && $authUser->user_type == BaseModel::INSTITUTE_USER_TYPE && $authUser->institute_id) {  //Institute User
            return $query->where($tableName . '.institute_id', $authUser->institute_id);
        }
        return $query;
    }
}
