<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class CourseSession
 * @package App\Models
 * @property int course_id
 * @property int course_config_id
 * @property int number_of_batches
 * @property Carbon application_start_date
 * @property Carbon application_end_date
 * @property Carbon course_start_date
 * @property int max_seat_available
 * @property-read CourseConfig $courseConfig
 * @property-read Course $course
 */
class CourseSession extends BaseModel
{
    protected $guarded = ['id'];

    protected $dates = [
        'application_start_date',
        'application_end_date',
        'course_start_date',
    ];

    public function courseConfig(): BelongsTo
    {
        return $this->belongsTo(CourseConfig::class);
    }
//
//    public function course(): BelongsTo
//    {
//        return $this->belongsTo(Course::class);
//    }
}
