<?php

namespace App\Events;

use App\Models\Batch;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\Log;

class CourseEnrollmentEvent
{
    public array $courseEnrollment;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $courseEnrollment)
    {
        $this->courseEnrollment = $courseEnrollment;
    }
}
