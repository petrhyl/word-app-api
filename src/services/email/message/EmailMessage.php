<?php

namespace services\email\message;

use services\email\message\participants\EmailParticipants;
use services\user\email\sender\message\IEmailMessage;

class EmailMessage implements IEmailMessage
{
    /**
     * @param EmailParticipants $participants e-mail addresses to, from and no-reply
     * @param string $subject e-mail subject
     * @param string $body e-mail body template as html
     * @param string $plainMessage e-mail message without html tags
     */
    public function __construct(
        public EmailParticipants $participants,
        public string $subject,
        public string $body,
        public string $plainMessage
    ) {}

    public function getParticipants(): EmailParticipants
    {
        return $this->participants;
    }

    public function getEmailSubject(): string
    {
        return $this->subject;
    }

    public function getHtmlContent(): string
    {
        return $this->body;
    }

    public function getPlainTextContent(): string
    {
        return $this->plainMessage;
    }
}
