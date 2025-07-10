<?php

namespace services\message\message\recipient;

class MessageRecipient
{
    public function __construct(
        public readonly string $to,
        public readonly string $recipientName = '',
        public readonly bool $useNoReplay = true
    ) {}
}
