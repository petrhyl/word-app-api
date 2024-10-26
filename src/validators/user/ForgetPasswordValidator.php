<?php

namespace validators\user;

use validators\common\Validator;
use validators\common\ValidatorUtils;

class ForgetPasswordValidator extends Validator
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param \models\request\ForgotPasswordRequest $object
     */
    public function validate($object): void
    {
        if (!ValidatorUtils::isEmailValid($object->email)) {
            $this->addInvalidProperty("email", "Invalid e-mail format.");
        }

        $this->throwExceptionIfAnyError();
    }
}