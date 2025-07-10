<?php

namespace endpoints\user;

use endpoints\BaseEndpoint;
use models\request\SendEmailRequest;
use services\user\UserService;

class SendVerificationEmail extends BaseEndpoint{
    public function __construct(private readonly UserService $userService) {}
    
    public function __invoke(SendEmailRequest $payload)
    {
        $this->userService->sendEmailToVerify($payload);

        $this->respondAndDie(["message" => "Email was successefully sent"]);
    }    
}