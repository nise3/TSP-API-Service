<?php

namespace App\Events\RplApplication;

class RplApplicationEvent
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
