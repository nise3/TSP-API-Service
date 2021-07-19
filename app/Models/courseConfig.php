<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class courseConfig
 * @package App\Models
 * @property int institute_id
 * @property int branch_id
 * @property int training_center
 * @property int programme_id
 * @property int course_id
 * @property int row_status
 * @property boolean ethnic
 * @property boolean freedom_fighter
 * @property boolean disable_status
 * @property boolean ssc
 * @property boolean hsc
 * @property boolean honors
 * @property boolean masters
 * @property boolean occupation
 * @property boolean guardian
 * @property-read Institute institute
 * @property-read Branch branch
 * @property-read TrainingCenter trainingCenter
 * @property-read Programme programme
 * @property-read Course course
 */
class courseConfig extends BaseModel
{
    protected $guarded = ['id'];
    //TODO: add relation function

    public function courseSessions():HasMany
    {
        return $this->hasMany(CourseSession::class);
    }
}
