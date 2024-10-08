<?php

namespace endpoints\user;

use endpoints\BaseEndpoint;
use models\request\ChangePasswordRequest;
use services\user\UserService;

class ChangePassword extends BaseEndpoint
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function __invoke(ChangePasswordRequest $payload)
    {
        $this->userService->changePassword($payload);

        $this->respondAndDie(null, 204);
    }
}
