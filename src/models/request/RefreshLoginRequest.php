<?php

namespace models\request;

class RefreshLoginRequest
{
    public int $userId;
    public string $accessToken;
    public string $refreshToken;
}
