<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class RtoCountry
 * @package App\Models
 * @property int id
 * @property string title_en
 * @property string title
 * @property Carbon |null created_at
 * @property Carbon |null updated_at
 * @property Carbon |null deleted_at
 */
class RtoCountry extends Model
{

    public $timestamps = false;
    /**
     * @var string[]
     */
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_ONLY_SOFT_DELETE;
}
