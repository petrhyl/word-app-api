<?php

namespace services\message\message\template;

enum MessageTemplateType
{
    case RegistrationVerification;
    case ConfirmedVerification;
    case ForgottenPassword;
}
