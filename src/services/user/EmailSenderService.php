<?php

namespace services\user;

use Exception;
use models\email\EmailMessage;
use models\email\EmailServerConfiguration;
use PHPMailer\PHPMailer\PHPMailer;

class EmailSenderService
{
    private readonly PHPMailer $mailer;

    public function __construct(
        private readonly EmailServerConfiguration $conf
    ) {
        $this->mailer = new PHPMailer();
    }

    public function sendMail(EmailMessage $message): void
    {
        $this->mailer->isSMTP();
        $this->mailer->Host = $this->conf->Server;;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $this->conf->SenderAddress;
        $this->mailer->Password = $this->conf->SenderPassword;
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = $this->conf->Port;

        //Recipients
        $this->mailer->setFrom($this->conf->SenderAddress, $this->conf->SenderName);
        $this->mailer->addCC($this->conf->SenderAddress);
        $this->mailer->addAddress($message->recipientAddress, $message->recipientName);

        // Content
        $this->mailer->isHTML(true);
        $this->mailer->Subject = $message->subject;
        $this->mailer->Body = $message->body;
        $this->mailer->AltBody = $message->plainMessage;

        $result = $this->mailer->send();

        if ($result === false) {
            throw new Exception($this->mailer->ErrorInfo, 101);
        }
    }
}
