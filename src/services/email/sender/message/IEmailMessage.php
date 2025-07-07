<?php

namespace services\user\email\sender\message;

interface IEmailMessage
{
    public function getParticipants(): IEmailParticipants;

    public function getEmailSubject(): string;

    public function getHtmlContent(): string;

    public function getPlainTextContent(): string;
}