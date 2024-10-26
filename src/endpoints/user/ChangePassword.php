<?php

namespace endpoints\user;

use endpoints\BaseEndpoint;
use models\request\ChangePasswordRequest;
use services\user\UserService;
use validators\user\ChangePasswordValidator;

class ChangePassword extends BaseEndpoint
{
    public function __construct(
        private readonly UserService $userService,
        private readonly ChangePasswordValidator $validator
    ) {}

    public function __invoke(ChangePasswordRequest $payload)
    {
        $this->validator->validate($payload);

        $response = $this->userService->changePassword($payload);

        $this->respondAndDie(['auth' => $response]);
    }
}
