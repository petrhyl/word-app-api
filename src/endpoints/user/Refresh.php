<?php

namespace endpoints\user;

use endpoints\BaseEndpoint;
use models\request\RefreshLoginRequest;
use services\user\UserService;

class Refresh extends BaseEndpoint
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function __invoke(RefreshLoginRequest $payload)
    {
        $response = $this->userService->refreshLogin($payload);

        $this->respondAndDie(["auth" => $response]);
    }
}
