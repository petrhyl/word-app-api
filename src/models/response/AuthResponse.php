<?php

namespace models\response;

class AuthResponse
{
    public int $userId;
    public string $name;
    public string $email;
    public string $accessToken;
    public string $accessTokenExpiresIn;
    public string $refreshToken;
    public string $refreshTokenExpireIn;
    public bool $isSubscriber;
    public bool $isVerified;
}
