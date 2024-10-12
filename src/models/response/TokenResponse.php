<?php

namespace models\response;

class TokenResponse
{
    public string $accessToken;
    public int $accessTokenExpiresIn;
    public string $refreshToken;
    public int $refreshTokenExpireIn;
}