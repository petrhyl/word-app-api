<?php

namespace services\message\sender;

use services\message\message\UserMessage;
use services\message\sender\type\MessageSenderType;

interface IMessageSender
{
    public static function type(): MessageSenderType;

    /**
     * @param UserMessage $message - message object filled to satisfy requirements of the particular sender
     * @throws \Exception - exception thrown when the sending of the message failed
     */
    public function send(
        UserMessage $message
    );
}
