<?php

namespace App\Events;

class MailSendEvent
{
    public array $mailPayload;

    /**
     * Create a new Email event instance.
     *
     * @return void
     */
    public function __construct(array $mailPayload)
    {
        $this->mailPayload = $mailPayload;

    }
}
