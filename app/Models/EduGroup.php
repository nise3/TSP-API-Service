<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\EduGroup
 *
 * @property int id
 * @property string code
 * @property string title
 * @property string title_en
 */
class EduGroup extends BaseModel
{
    use SoftDeletes, HasFactory;

    public $timestamps = false;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_ONLY_SOFT_DELETE;
}
