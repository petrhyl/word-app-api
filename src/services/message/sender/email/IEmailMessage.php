<?php

namespace services\message\sender\email;

interface IEmailMessage
{
    public function getSubject(): string;
    public function getContent(): string;
    public function getPlainTextContent(): string;
    public function getRecipientAddress(): string;
    public function getRecipientName(): string;
    public function isNoReply(): bool;
}
