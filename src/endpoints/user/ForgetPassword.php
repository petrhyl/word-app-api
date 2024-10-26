<?php

namespace endpoints\user;

use endpoints\BaseEndpoint;
use models\request\ForgotPasswordRequest;
use services\user\UserService;
use validators\user\ForgetPasswordValidator;

class ForgetPassword extends BaseEndpoint
{
    public function __construct(
        private readonly UserService $userService,
        private readonly ForgetPasswordValidator $validator
    ) {}

    public function __invoke(ForgotPasswordRequest $payload)
    {
        $this->validator->validate($payload);

        $this->userService->forgetPassword($payload);

        return $this->respondAndDie(null, 204);
    }
}
