<?php

namespace models\request;

class ChangePasswordRequest
{
    public string $previousPassword;
    public string $newPassword;
}
