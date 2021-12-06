<?php

namespace App\Traits\Scopes;

use App\Models\User;
use Illuminate\Support\Facades\Schema;

trait ScopePublicInstitute
{

    /**
     * @param $query
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function ScopePublicInstitute($query): mixed
    {
        $tableName = $this->getTable();
        $instituteId = last(request()->segments());
        if(is_numeric($instituteId)){
            return $query->where($tableName . '.institute_id', $instituteId);
        }
        return $query;
    }
}
