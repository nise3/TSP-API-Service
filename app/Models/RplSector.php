<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Role
 *
 * @property int id
 * @property string title_en
 * @property string title
 * @property string description
 * @property array translations
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class RplSector extends BaseModel
{
    use ScopeRowStatusTrait, SoftDeletes;

    /**
     * @var string[]
     */
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    protected $casts = [
        'translations' => 'array'
    ];


    public function getTitleAttribute()
    {
        if(request()->get('country_id') && $this->attributes['translations']){
           $translations = json_decode($this->attributes['translations'],true);
           return $translations[request()->get('country_id')]['title'];
        }
        return $this->attributes['title'];
    }

}
