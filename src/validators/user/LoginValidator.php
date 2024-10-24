<?php

namespace validators\user;

use validators\common\ValidatorUtils;
use validators\common\Validator;

class LoginValidator extends Validator
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
            $this->addInvalidProperty("email", "Invalid e-mail format.");
        }

        if (empty($object->password)) {
            $this->addInvalidProperty("password", "Password can not be empty.");
        }

        $this->throwExceptionIfAnyError();
    }
}
