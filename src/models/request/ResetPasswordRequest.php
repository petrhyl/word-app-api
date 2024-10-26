<?php

namespace models\request;

class ResetPasswordRequest
{
    public string $password;
    public string $verificationKey;
}
