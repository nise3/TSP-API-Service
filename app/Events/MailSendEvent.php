<?php

namespace App\Events;

class MailSendEvent
{
    public array $data;
    /**
     * Create a new Email event instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
}
