<?php

namespace App\Services\CommonServices;

use App\Events\MailSendEvent;
use App\Events\SmsSendEvent;

class SmsService
{
    private string $recipient;
    private string $message;

    /**
     * @param string $recipient
     * @param string $message
     */
    public function __construct(string $recipient, string $message)
    {
        $this->recipient = $recipient;
        $this->message = $message;
    }

    /**
     * @param string $recipient
     */
    public function setRecipient(string $recipient): void
    {
        $this->recipient = $recipient;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function sendSms(){
        $smsConfig=[
            "recipient"=>$this->recipient,
            "message"=>$this->message
        ];
        event(new SmsSendEvent($smsConfig));
    }

}
