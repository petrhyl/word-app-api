<?php

namespace endpoints\user;

use endpoints\BaseEndpoint;
use models\request\LogoutRequest;
use services\user\UserService;

class Logout extends BaseEndpoint
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function __invoke(LogoutRequest $payload)
    {
        $this->userService->logout($payload);

        $this->respondAndDie(null, 204);
    }
}
