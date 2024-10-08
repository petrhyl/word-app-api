<?php

namespace validators;

use validators\common\ValidatorUtils;
use validators\common\Validator;

class LoginRequestValidator extends Validator
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param \models\request\LoginRequest $object
     */
    public function validate($object): void
    {
        if (!ValidatorUtils::isEmailValid($object->email)) {
            $this->addInvalidPropertyName("email");
            $this->addMessage("Invalid e-mail format.");
        }

        if (empty($object->password)) {
            $this->addInvalidPropertyName("password");
            $this->addMessage("Password can not be empty.");
        }

        $this->throwExceptionIfAnyError();
    }
}
