<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrganizationType
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 * @property bool is_government
 */
class OrganizationType extends BaseModel
{
    use ScopeRowStatusTrait;
    /**
     * @var string[]
     */
    protected  $guarded = ['id'];
}
