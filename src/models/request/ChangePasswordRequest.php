<?php

namespace models\request;

class ChangePasswordRequest
{
    public string $userEmail;
    public string $verificationKey;
    public string $newPassword;
}
