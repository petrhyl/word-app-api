<?php

namespace endpoints\user;

use endpoints\BaseEndpoint;
use models\request\UpdateUserRequest;
use services\user\UserService;

class UpdateUser extends BaseEndpoint
{
    public function __construct(private readonly UserService $userService) {}

    public function __invoke(UpdateUserRequest $payload)
    {
        $response = $this->userService->updateUserData($payload);

        $this->respondAndDie(["user" => $response]);
    }
}
