<?php

namespace App\Events\BatchCalender;

use Illuminate\Support\Facades\Log;

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
        Log::info(json_encode($data));
        $this->data = $data;
    }
}
