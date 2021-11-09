<?php

namespace App\Traits\Scopes;

use App\Models\BaseModel;
use Illuminate\Support\Facades\Auth;

trait ScopeFilterByInstitute
{
    public function scopeByInstitute($query, $table)
    {
        $authUser = Auth::user();

        if ($authUser && $authUser->user_type == BaseModel::INSTITUTE_USER_TYPE && $authUser->institute_id) {  //Institute User
            return $query->where($table . '.institute_id', $authUser->institute_id);
        }
        return $query;
    }
}
