<?php

namespace services\user\email\sender\message;

interface IEmailParticipants
{
    public function getRecipientAddress(): string;

    public function getRecipientName(): string;

    public function useNoReply(): bool;

    public function hasBccAddresses(): bool;

    public function getBccAddresses(): array;
}