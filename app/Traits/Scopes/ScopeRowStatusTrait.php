<?php

namespace App\Traits\Scopes;


use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait ScopeRowStatusTrait
 * @package App\Traits\ModelTraits
 * @method static Builder active()
 * @method static Builder inactive()
 * @method static Builder deleted()
 * @property int row_status
 */
trait ScopeRowStatusTrait
{
    public function scopeActive($query): Builder
    {
        /**  @var Builder $query */
        return $query->where('row_status', BaseModel::ROW_STATUS_ACTIVE);
    }

    public function scopeInactive($query): Builder
    {
        /**  @var Builder $query */
        return $query->where('row_status', BaseModel::ROW_STATUS_INACTIVE);
    }

}
