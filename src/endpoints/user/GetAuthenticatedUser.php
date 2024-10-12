<?php

namespace endpoints\user;

use endpoints\BaseEndpoint;
use services\user\UserService;

class GetAuthenticatedUser extends BaseEndpoint
{
    public function __construct(private readonly UserService $userService) {}

    public function __invoke()
    {
        $response = $this->userService->getAuthenticatedUser();

        $this->respondAndDie(["user" => $response], 200);
    }
}
