<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $dates = [
        'application_start_date',
        'application_end_date',
        'course_start_date',
    ];

    /**
     * @return BelongsTo
     */
    public function courseConfig(): BelongsTo
    {
        return $this->belongsTo(CourseConfig::class);
    }

    /**
     * @return BelongsTo
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
