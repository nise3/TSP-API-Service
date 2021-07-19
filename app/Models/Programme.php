<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Institute;
use App\Traits\scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Programme
 * @package App\Models
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 * @property int institute_id
 * @property string|null logo
 * @property string code
 * @property string description
 */
class Programme extends BaseModel
{
    use  ScopeRowStatusTrait;

    protected $guarded = ['id'];

    protected $fillableble = ['title_en', 'title_bn', 'institute_id', 'code', 'description','logo'];


    const DEFAULT_LOGO = 'programme/default.jpg';

    /**
     * @return BelongsTo
     */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }

    //TO DO
//    public function publishCourses(): HasMany
//    {
//        return $this->hasMany(PublishCourse::class);
//    }

    /**
     * @return bool
     */
    public function logoIsDefault(): bool
    {
        return $this->logo === self::DEFAULT_LOGO;
    }
}
