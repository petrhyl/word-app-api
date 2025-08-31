<?php

namespace endpoints\user;

use endpoints\BaseEndpoint;
use models\request\RefreshTokensRequest;
use services\user\UserService;

class Refresh extends BaseEndpoint
{
    public function __construct(private readonly UserService $userService) {}

    public function __invoke(RefreshTokensRequest $payload)
    {
        $response = $this->userService->refreshTokens($payload);

        $this->respondAndDie(["authToken" => $response]);
    }
}
