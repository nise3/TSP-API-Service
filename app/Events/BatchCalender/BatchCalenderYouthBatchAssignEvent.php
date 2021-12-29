<?php

namespace App\Events\BatchCalender;

class BatchCalenderYouthBatchAssignEvent
{

    public array $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
}
