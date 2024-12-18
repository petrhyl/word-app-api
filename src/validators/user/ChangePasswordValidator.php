<?php

namespace validators\user;

use validators\common\Validator;
use validators\common\ValidatorUtils;

class ChangePasswordValidator extends Validator
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param \models\request\ChangePasswordRequest $object
     */
    public function validate($object): void
    {
        if(empty($object->previousPassword)) {
            $this->addInvalidProperty("previousPassword", "Old password is required.");
        }

        if (!ValidatorUtils::isPasswordValid($object->newPassword)) {
            $this->addInvalidProperty("password", "Password has to contain at least one uppercase letter, one digit and has to be at least 8 characters long.");
        }

        $this->throwExceptionIfAnyError();
    }
}