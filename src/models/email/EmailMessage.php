<?php

namespace models\email;

class EmailMessage
{
    public string $recipientAddress;
    /**
     * @var string $recipientName is not required
     * * default value is empty string
     */
    public string $recipientName = '';
    public string $subject;
    public string $body;
    /**
     * @var string $plainMessage e-mail message without html tags
     */
    public string $plainMessage;
}
