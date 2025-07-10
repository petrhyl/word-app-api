<?php

namespace services\message\sender\email;

use PHPMailer\PHPMailer\PHPMailer;
use services\message\sender\IMessageSender;
use services\message\sender\type\MessageSenderType;

class EmailSender implements IMessageSender
{
    private readonly PHPMailer $mailer;

    public function __construct(
        private readonly EmailSenderConfiguration $conf
    ) {
        $this->mailer = new PHPMailer(true);
        $this->setServer();
    }

    public static function type(): MessageSenderType
    {
        return MessageSenderType::email;
    }

    public function send(IEmailMessage $message)
    {
        $this->setEmailBody($message);

        try {
            $this->setParticipantsAddresses($message);

            // * PHPMailer is set to throw exceptions on error
            $this->mailer->send();
        } catch (\Throwable $e) {
            throw $e;
        } finally {
            $this->clearAllAddressesAndAttachments();
        }
    }


    public function setParticipantsAddresses(IEmailMessage $message)
    {
        $this->mailer->setFrom($this->conf->senderAddress, $this->conf->senderName);
        $this->mailer->addAddress($message->getRecipientAddress(), $message->getRecipientName());

        if ($this->conf->addSenderToBcc) {
            $this->mailer->addBCC($this->conf->senderAddress);
        }

        if ($message->isNoReply()) {
            $this->mailer->addReplyTo($this->conf->noReplyEmail, $this->conf->senderName);
        } else {
            $this->mailer->addReplyTo($this->conf->senderAddress, $this->conf->senderName);
        }
    }

    public function setEmailBody(IEmailMessage $message)
    {
        $this->mailer->isHTML(true);
        $this->mailer->Subject = $message->getSubject();
        $this->mailer->Body = $message->getContent();
        $this->mailer->AltBody = $message->getPlainTextContent();
    }

    public function clearAllAddressesAndAttachments()
    {
        $this->mailer->clearAddresses();
        $this->mailer->clearAttachments();
        $this->mailer->clearAllRecipients();
        $this->mailer->clearCCs();
        $this->mailer->clearReplyTos();
    }

    private function setServer()
    {
        $this->mailer->isSMTP();
        $this->mailer->Host = $this->conf->server;;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $this->conf->senderAddress;
        $this->mailer->Password = $this->conf->senderPassword;
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = $this->conf->port;

        $this->mailer->Encoding = PHPMailer::ENCODING_BASE64;
        $this->mailer->CharSet = PHPMailer::CHARSET_UTF8;
    }
}
