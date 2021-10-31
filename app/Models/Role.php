<?php

namespace App\Models;

use Carbon\Carbon;

/**
 * App\Models\Role
 *
 * @property int id
 * @property string title_en
 * @property string title
 * @property string description
 * @property string key
 * @property int permission_group_id
 * @property int permission_sub_group_id
 * @property int organization_id
 * @property int institute_id
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Role extends BaseModel
{
    protected $guarded = [];
}
