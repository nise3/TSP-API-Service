<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Carbon\Carbon;

/**
 * App\Models\EduGroup
 *
 * @property int id
 * @property int row_status
 * @property string code
 * @property string title
 * @property string title_en
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class EduGroup extends BaseModel
{
    use ScopeRowStatusTrait;
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
}
