<?php

namespace services\message\message\template;

use InvalidArgumentException;

class MessageTemplateAccessor
{
    public function getTemplate(MessageTemplateType $type, array $variables, string $lang = 'en'): MessageTemplate
    {
        switch ($type) {
            case MessageTemplateType::RegistrationVerification:
                return new MessageTemplate(
                    1,
                    MessageTemplateType::RegistrationVerification,
                    "E-mail verification",
                    "<h2 style=\"margin: 25px auto 10px 15px\">Hello from Word App</h2>
                    <p style=\"margin: 0px auto 35px 15px;font-size: 1.2em;font-weight: 600\">Vocabulary practicing</p>
                    <h3>Thank you for your registration on our web site.</h3>
                    <p>Please, verify your e-mail addres to fully enjoy our web application.</p>        
                    <p>To verify your e-mail address please use this link by clicking on it: <a style=\"color: #004745; font-weight: 600; font-size: 1.2em\" href=\"{*verificationLink*}/{*verificationKey*}\">Verification</a></p>
                    <p>&nbsp;</p>
                    <p style=\"font-size: 0.9em\">If this request wasn't made by you, please disregard or delete this email.</p>",
                    $variables
                );
            case MessageTemplateType::ConfirmedVerification:
                return new MessageTemplate(
                    2,
                    MessageTemplateType::ConfirmedVerification,
                    "E-mail verified",
                    "<h2 style=\"margin: 25px auto 35px 15px\">Hello from Word App</h2>
                    <p style=\"margin: 0px auto 35px 15px;font-size: 1.2em;font-weight: 600\">Vocabulary practicing</p>
                    <h3>Thank you for your registration on our web site.</h3>
                    <p>Your e-mail address was successfully verified.</p>
                    <p>We hope you will like our web application for vocabulary learning.</p>
                    <p>You can enjoy our application after logging in at this link:  <a style=\"color: #004745; font-weight: 600; font-size: 1.2em\" href=\"{*loginLink*}\">Log In</a></p>
                    <p>&nbsp;</p>
                    <p style=\"font-size: 0.9em\">If this email doesn't belong to you, please ignore or delete it.</p>",
                    $variables
                );
            case MessageTemplateType::ForgottenPassword:
                return new MessageTemplate(
                    3,
                    MessageTemplateType::ForgottenPassword,
                    "Reset password",
                    "<h2 style=\"margin: 25px auto 35px 15px\">Hello from Word App</h2>
                    <p style=\"margin: 0px auto 35px 15px;font-size: 1.2em;font-weight: 600\">Vocabulary practicing</p>
                    <h3>We received a request to reset your password.</h3>
                    <p>Your password has been reset, you can't use it yet.</p>
                    <p>You can reset your password at this link: <a style=\"color: #004745; font-weight: 600; font-size: 1.2em\" href=\"{*resetLink*}/{*resetKey*}\">Reset Password</a></p>
                    <p>&nbsp;</p>
                    <p style=\"font-size: 0.9em\">If this email doesn't belong to you, please ignore or delete it.</p>",
                    $variables
                );
            default:
                throw new InvalidArgumentException("Invalid template ID", 1);
        }
    }
}
