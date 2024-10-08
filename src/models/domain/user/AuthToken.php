<?php

namespace models\domain\user;

class AuthToken{
    public string $Value;
    /**
     * @var int $ExpireIn timestamp
     */
    public int $ExpireIn;
}