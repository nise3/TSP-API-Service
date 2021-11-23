<?php

namespace App\Events;

class BatchEvent
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
