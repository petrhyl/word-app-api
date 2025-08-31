<?php

namespace models\response;

class AuthToken
{
    public function __construct(
        public string $token,
        public int $expiresIn
    ) {}
}
