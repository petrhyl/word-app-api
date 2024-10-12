<?php

namespace models\request;

class RefreshTokensRequest
{
    public string $accessToken;
    public string $refreshToken;
}
