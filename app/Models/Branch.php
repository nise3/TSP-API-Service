<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Branch
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 * @property int institute_id
 * @property string|null address
 * @property string|null google_map_src
 * @method static \Illuminate\Database\Eloquent\Builder|Institute acl()
 */

class Branch extends BaseModel
{


    protected $guarded = ['id'];
    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }

    public function publishCourses(): HasMany
    {
        return $this->hasMany(PublishCourse::class);
    }
}
