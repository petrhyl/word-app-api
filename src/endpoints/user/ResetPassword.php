<?php

namespace endpoints\user;

use endpoints\BaseEndpoint;
use models\request\ResetPasswordRequest;
use services\user\UserService;
use validators\user\ResetPasswordRequestValidator;

class ResetPassword extends BaseEndpoint
{
    public function __construct(
        private readonly UserService $userService,
        private readonly ResetPasswordRequestValidator $validator
    ) {}

    public function __invoke(ResetPasswordRequest $payload)
    {
        $this->validator->validate($payload);

        $this->userService->resetPassword($payload);

        $this->respondAndDie(null, 204);
    }
}
