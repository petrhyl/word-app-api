<?php

namespace models\response;

class TokenResponse
{
    public AuthToken $accessToken;
    public AuthToken $refreshToken;
}