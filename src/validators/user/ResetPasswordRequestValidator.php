<?php

namespace validators\user;

use validators\common\Validator;
use validators\common\ValidatorUtils;

class ResetPasswordRequestValidator extends Validator
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param \models\request\NewPasswordRequest $object
     */
    public function validate($object): void
    {
        if(empty($object->verificationKey)) {
            $this->addInvalidProperty("verificationKey", "Verification key is required.");
        }

        if (!ValidatorUtils::isPasswordValid($object->password)) {
            $this->addInvalidProperty("password", "Password has to contain at least one uppercase letter, one digit and has to be at least 8 characters long.");
        }

        $this->throwExceptionIfAnyError();
    }
}