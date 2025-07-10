<?php

namespace services\user\auth\configuration;

use Exception;
use services\message\sender\type\MessageSenderType;

class AuthConfiguration
{
    public readonly string $senderType;
    public readonly string $verificationLink;
    public readonly string $loginLink;
    public readonly string $resetLink;
    public readonly bool $useNoReply;

    public function getMessageSender(): MessageSenderType
    {
        foreach (MessageSenderType::cases() as $sender) {
            if ($sender->name === $this->senderType) {
                return $sender;
            }
        }

        throw new Exception("Invalid sender type", 101);
    }
}
