<?php

namespace endpoints\user;

use endpoints\BaseEndpoint;
use services\user\EmailAddressService;
use validators\VerificationKeyValidator;

class Verification extends BaseEndpoint
{
    public function __construct(
        private readonly EmailAddressService $emailService,
        private readonly VerificationKeyValidator $validator
    ) {
    }

    public function __invoke(string $key)
    {
        $this->validator->validate($key);

        $this->emailService->verify($key);

        $this->respondAndDie(["message" => "E-mail was successfully verified"], 200);
    }
}
