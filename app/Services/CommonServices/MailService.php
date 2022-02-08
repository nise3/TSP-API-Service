<?php

namespace App\Services\CommonServices;

use App\Events\MailSendEvent;
use App\Models\BaseModel;
use App\Models\Batch;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class MailService
{
    public const RECIPIENT_NAME = 'Nise';


    /** FILE_EXTENSION_ALLOWABLE KEY */
    public const ALLOWABLE_PDF = "pdf";
    public const ALLOWABLE_DOC = "doc";
    public const ALLOWABLE_DOCX = "docx";
    public const ALLOWABLE_CSV = "csv";
    public const ALLOWABLE_XLS = "xls";
    public const ALLOWABLE_XLSX = "xlsx";
    public const ALLOWABLE_TEXT = "text";
    public const ALLOWABLE_TXT = "txt";
    public const ALLOWABLE_JPEG = "jpeg";
    public const ALLOWABLE_JPG = "jpg";
    public const ALLOWABLE_JPE = "jpe";
    public const ALLOWABLE_PNG = "png";

    const FILE_EXTENSION_ALLOWABLE = [
        self::ALLOWABLE_PDF,
        self::ALLOWABLE_DOC,
        self::ALLOWABLE_DOCX,
        self::ALLOWABLE_CSV,
        self::ALLOWABLE_XLS,
        self::ALLOWABLE_XLSX,
        self::ALLOWABLE_TEXT,
        self::ALLOWABLE_TXT,
        self::ALLOWABLE_JPEG,
        self::ALLOWABLE_JPE,
        self::ALLOWABLE_JPG,
        self::ALLOWABLE_PNG
    ];

    private array $to;
    private string $from;
    private string $recipientName;
    private string $replyTo;
    private string $subject;
    private string $messageBody;
    private array $cc;
    private array $bcc;
    private array $attachments;


    /**
     * @param array $to
     * @param string $from
     * @param string $subject
     * @param string $messageBody
     */
    public function __construct(array $to, string $from, string $subject, string $messageBody)
    {
        $this->to = $to;
        $this->from = $from;
        $this->subject = $subject;
        $this->messageBody = $messageBody;
    }

    /**
     * @param string $recipientName
     */
    public function setRecipientName(string $recipientName): void
    {
        $this->recipientName = $recipientName;
    }

    /**
     * @param string $replyTo
     */
    public function setReplyTo(string $replyTo): void
    {
        $this->replyTo = $replyTo;
    }

    /**
     * @param array $cc
     */
    public function setCc(array $cc): void
    {
        $this->cc = $cc;
    }

    /**
     * @param array $bcc
     */
    public function setBcc(array $bcc): void
    {
        $this->bcc = $bcc;
    }

    /**
     * @param array $attachments
     */
    public function setAttachments(array $attachments): void
    {
        $this->attachments = $attachments;
    }


    public function sendMail()
    {
        $sendMailPayload = [
            'to' => $this->to,
            'from' => $this->from,
            'subject' => $this->subject,
            'message_body' => $this->messageBody,
            'name' => !empty($this->recipientName) ? $this->recipientName : self::RECIPIENT_NAME
        ];

        if (!empty($this->replyTo)) {
            $sendMailPayload['reply_to'] = $this->replyTo;
        }
        if (!empty($this->cc)) {
            $sendMailPayload['cc'] = $this->cc;
        }
        if (!empty($this->bcc)) {
            $sendMailPayload['bcc'] = $this->bcc;
        }
        if (!empty($this->attachments)) {
            $sendMailPayload['attachment'] = $this->attachments;
        }

        Log::channel('mail_sms')->info('MailPayload: ' . json_encode($sendMailPayload));

        event(new MailSendEvent($sendMailPayload));
    }

    /**
     * @throws Throwable
     */
    public static function templateView(string $message): string
    {
        $template = 'mail.ssp-default-template';
        return view($template, compact('message'))->render();
    }
}
