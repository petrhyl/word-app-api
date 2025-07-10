<?php

namespace services\message\message;

use services\message\message\recipient\MessageRecipient;
use services\message\message\template\MessageTemplate;
use services\message\sender\email\IEmailMessage;
use services\message\sender\sms\ISMSMessage;

class UserMessage implements IEmailMessage, ISMSMessage
{
    public function __construct(
        public readonly MessageRecipient $recipient,
        public readonly MessageTemplate $template
    ) {}

    public function getSubject(): string
    {
        return $this->template->subject;
    }

    public function getRecipientAddress(): string
    {
        return $this->recipient->to;
    }

    public function getRecipientPhoneNumber(): string
    {
        return $this->recipient->to;
    }

    public function getRecipientName(): string
    {
        return $this->recipient->recipientName;
    }

    public function getSenderName(): string
    {
        return $this->senderName ?? '';
    }

    public function getContent(): string
    {
        return $this->template->getBody();
    }

    public function getPlainTextContent(): string
    {
        return strip_tags($this->template->getBody());
    }

    public function isNoReply(): bool
    {
        return $this->recipient->useNoReplay;
    }
}
