<?php

namespace models\email\sender\configuration;

class EmailSenderConfiguration
{
    public readonly string $server;
    public readonly string $senderAddress;
    public readonly string $senderName;
    public readonly string $senderPassword;
    public readonly int $port;
    public readonly bool $addSenderToBcc;
    public readonly string $noReplyEmail;
}
