<?php

namespace models\request;

class LogoutRequest
{
    public int $userId;
    public string $refreshToken;
}
