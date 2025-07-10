<?php

namespace services\message\sender\sms;

interface ISMSMessage
{
    public function getRecipientPhoneNumber(): string;

    public function getContent(): string;

    public function getSenderName(): string;
}
