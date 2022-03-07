<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class RplLevel extends BaseModel
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
            return $translations[request()->get('country_id')]['title'] ?? $this->attributes['title'];
        }
        return $this->attributes['title'];
    }
}
