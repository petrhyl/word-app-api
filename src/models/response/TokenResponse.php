<?php

namespace models\response;

class TokenResponse
{
    public function __construct(
        public AuthToken $accessToken,
        public AuthToken $refreshToken
    ) {}
}
