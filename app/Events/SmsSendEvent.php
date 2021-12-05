<?php

namespace App\Events;

class SmsSendEvent
{
    public array $data;
    /**
     * Create a new SMS event instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
}
