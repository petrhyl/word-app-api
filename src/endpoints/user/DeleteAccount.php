<?php

namespace endpoints\user;

use services\user\UserService;

class DeleteAccount{
    public function __construct(private readonly UserService $userService) {
    }

    public function __invoke()
    {
        
    }
}