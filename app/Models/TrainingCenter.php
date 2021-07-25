<?php

namespace App\Models;

use App\Traits\scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TrainingCenter
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 * @property int institute_id
 * @property int branch_id
 * @property string|null address
 * @property string|null google_map_src
 * @property int row_status
 * @property-read  Institute institute
 */
class TrainingCenter extends BaseModel
{
    use  ScopeRowStatusTrait;

    protected $guarded = ['id'];


    /**
     * @return BelongsTo
     */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }

//    public function branch(): BelongsTo
//    {
//        return $this->belongsTo(Branch::class);
//    }

//    public function publishCourses(): HasMany
//    {
//        return $this->hasMany(PublishCourse::class);
//    }

}
