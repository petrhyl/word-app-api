<?php

namespace endpoints\user;

use endpoints\BaseEndpoint;
use models\request\LoginRequest;
use services\user\UserService;
use validators\user\LoginValidator;

class Login extends BaseEndpoint
{
    public function __construct(
        private readonly UserService $userService,
        private readonly LoginValidator $validator
    ) {}

    public function __invoke(LoginRequest $payload)
    {
        $this->validator->validate($payload);

        $respose = $this->userService->login($payload);

        $this->respondAndDie(["auth" => $respose]);
    }
}
