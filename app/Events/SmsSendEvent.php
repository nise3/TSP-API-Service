<?php

namespace App\Events;

use Illuminate\Support\Facades\Log;

class SmsSendEvent
{
    public array $smsPayload;

    /**
     * Create a new SMS event instance.
     *
     * @return void
     */
    public function __construct(array $smsPayload)
    {
        $this->smsPayload = $smsPayload;
        Log::info("set-in-sms-event".json_encode($this->smsPayload));
    }
}
