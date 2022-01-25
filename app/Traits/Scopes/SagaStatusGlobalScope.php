<?php

namespace App\Traits\Scopes;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Scope;

class SagaStatusGlobalScope implements Scope
{
    /**
     * @inheritDoc
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where($builder->getModel()->getTable().'.saga_status', '=', BaseModel::SAGA_STATUS_COMMIT);
    }
}
