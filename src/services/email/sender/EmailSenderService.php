<?php

namespace services\email\sender;

use config\ErrorHandler;
use models\email\sender\configuration\EmailSenderConfiguration;
use PHPMailer\PHPMailer\PHPMailer;
use services\user\email\sender\message\IEmailMessage;
use services\user\email\sender\message\IEmailParticipants;

class EmailSenderService
{
    private readonly PHPMailer $mailer;

    public function __construct(
        private readonly EmailSenderConfiguration $conf
    ) {
        $this->mailer = new PHPMailer(true);
        $this->setServer();
    }

    /**
     * @return bool true if the email was sent successfully or false if it failed
     */
    public function sendMessage(IEmailMessage $message): bool
    {
        $this->setEmailBody($message);

        $result = true;

        try {
            $this->setParticipantsAddresses($message->getParticipants());

            // * PHPMailer is set to throw exceptions on error
            $this->mailer->send();
            $result = true;
        } catch (\Throwable $e) {
            ErrorHandler::logErrors(ErrorHandler::formatExceptionToLog($e));
            $result = false;
        } finally {
            $this->clearAllAddressesAndAttachments();
        }

        return $result;
    }

    public function setParticipantsAddresses(IEmailParticipants $participants)
    {
        $this->mailer->setFrom($this->conf->senderAddress, $this->conf->senderName);
        $this->mailer->addAddress($participants->getRecipientAddress(), $participants->getRecipientName());

        if ($this->conf->addSenderToBcc) {
            $this->mailer->addBCC($this->conf->senderAddress);
        }

        if ($participants->hasBccAddresses()) {
            foreach ($participants->getBccAddresses() as $bcc) {
                $this->mailer->addBCC($bcc);
            }
        }

        if ($participants->useNoReply()) {
            $this->mailer->addReplyTo($this->conf->noReplyEmail, $this->conf->senderName);
        } else {
            $this->mailer->addReplyTo($this->conf->senderAddress, $this->conf->senderName);
        }
    }

    public function setEmailBody(IEmailMessage $message)
    {
        $this->mailer->isHTML(true);
        $this->mailer->Subject = $message->getEmailSubject();
        $this->mailer->Body = $message->getHtmlContent();
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
