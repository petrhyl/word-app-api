<?php

namespace endpoints\user;

use endpoints\BaseEndpoint;
use models\request\UpdateUserRequest;
use services\user\UserService;
use validators\user\UpdateUserValidator;

class UpdateUser extends BaseEndpoint
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UpdateUserValidator $validator
    ) {}

    public function __invoke(UpdateUserRequest $payload)
    {
        $this->validator->validate($payload);

        $response = $this->userService->updateUserData($payload);

        $this->respondAndDie(["user" => $response]);
    }
}
