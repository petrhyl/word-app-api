<?php

namespace services\message;

use config\ErrorHandler;
use InvalidArgumentException;
use services\message\message\recipient\MessageRecipient;
use services\message\message\template\MessageTemplateAccessor;
use services\message\message\template\MessageTemplateType;
use services\message\message\UserMessage;
use services\message\sender\email\EmailSender;
use services\message\sender\email\EmailSenderConfiguration;
use services\message\sender\IMessageSender;
use services\message\sender\type\MessageSenderType;

class UserMessageService
{
    private ?EmailSender $emailSender = null;

    public function __construct(
        private readonly EmailSenderConfiguration $emailConf,
        private readonly MessageTemplateAccessor $templateAccessor
    ) {}

    /**
     * Sends a message to the specified recipient using the provided template ID and parameters.
     *
     * @param MessageRecipient $recipient The recipient of the message
     * @param MessageSenderType $sender The type of message to be sent.
     * @param MessageTemplateType $template The message template to use
     * @param array $variables key-value pairs of parameters to fill in the template
     */
    public function send(MessageRecipient $recipient, MessageSenderType $sender, MessageTemplateType $template, array $variables): void
    {
        $sender = $this->getSenderOfType($sender);
        $message = new UserMessage(
            $recipient, 
            $this->templateAccessor->getTemplate($template, $variables)
        );

        try {
            $sender->send($message);
        } catch (\Throwable $th) {
            ErrorHandler::logErrors(ErrorHandler::formatExceptionToLog($th));
        }
    }

    /**
     * Returns an instance of IMessageSender based on the provided MessageSenderType.
     *
     * @param MessageSenderType $type
     * @return IMessageSender
     * @throws InvalidArgumentException if the type is not supported
     */
    public function getSenderOfType(MessageSenderType $type): IMessageSender
    {
        switch ($type) {
            case MessageSenderType::email:
                return $this->getEmailSender();
            default:
                throw new InvalidArgumentException("Unsupported message sender type: $type");
        }
    }

    private function getEmailSender(): EmailSender
    {
        if ($this->emailSender === null) {
            $this->emailSender = new EmailSender($this->emailConf);
        }

        return $this->emailSender;
    }
}
