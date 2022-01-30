<?php

namespace App\Services\CommonServices;

use App\Events\MailSendEvent;
use App\Events\SmsSendEvent;

class SmsService
{

    /**
     * @param string $recipient
     * @param string $message
     */
    public function sendSms(string $recipient, string $message)
    {
        $smsConfig = [
            "recipient" => $recipient,
            "message" => $message
        ];
        event(new SmsSendEvent($smsConfig));
    }

}
