<?php

namespace endpoints\user;

use endpoints\BaseEndpoint;
use services\user\UserService;
use validators\user\VerificationKeyValidator;

class Verification extends BaseEndpoint
{
    public function __construct(
        private readonly VerificationKeyValidator $validator,
        private readonly UserService $userService
    ) {
    }

    public function __invoke(string $key)
    {
        $this->validator->validate($key);

        $this->userService->verify($key);

        $this->respondAndDie(["message" => "E-mail was successfully verified"], 200);
    }
}
