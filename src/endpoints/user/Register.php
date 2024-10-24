<?php

namespace endpoints\user;

use endpoints\BaseEndpoint;
use models\request\RegisterRequest;
use services\user\UserService;
use validators\user\RegisterValidator;

class Register extends BaseEndpoint
{
    public function __construct(
        private readonly UserService $userService,
        private readonly RegisterValidator $validator
    ) {}

    public function __invoke(RegisterRequest $payload)
    {
        $this->validator->validate($payload);

        $response = $this->userService->register($payload);

        $this->respondAndDie($response, 201);
    }
}
