<?php

namespace services\email\message\participants;

use services\user\email\sender\message\IEmailParticipants;

class EmailParticipants implements IEmailParticipants
{
    /**
     * @param string $recipientAddress e-mail address which the e-mail will be sent to
     * @param string $recipientName (optional)
     * @param bool $useNoReply (optional) e-mail will be sent with no-reply address in headers
     * @param array $bccAddresses (optional) e-mail addresses to send a blind carbon copy - for bulk e-mails
     */
    public function __construct(
        public string $recipientAddress,
        public string $recipientName = '',
        public bool $useNoReply = false,
        public array $bccAddresses = []
    ) {}

    public function getRecipientAddress(): string
    {
        return $this->recipientAddress;
    }

    public function getRecipientName(): string
    {
        return $this->recipientName;
    }

    public function useNoReply(): bool
    {
        return $this->useNoReply;
    }

    public function hasBccAddresses(): bool
    {
        return count($this->bccAddresses) > 0;
    }

    public function getBccAddresses(): array
    {
        return $this->bccAddresses;
    }
}
