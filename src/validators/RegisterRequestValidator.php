<?php

namespace validators;

use validators\common\ValidatorUtils;
use validators\common\Validator;

class RegisterRequestValidator extends Validator
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param \models\request\RegisterRequest $object
     */
    public function validate($object): void
    {
        if (!ValidatorUtils::isEmailValid($object->email)) {
            $this->addInvalidPropertyName("email");
            $this->addMessage("Invalid e-mail format.");
        }

        if (empty($object->name)) {
            $this->addInvalidPropertyName("name");
            $this->addMessage("Name can not be empty.");
        } elseif (!ValidatorUtils::isNameValid($object->name)) {
            $this->addInvalidPropertyName("name");
            $this->addMessage("Name can only contain letters or hyphen, spaces, apostrophe.");
        }

        if (!self::isPasswordValid($object->password)) {
            $this->addInvalidPropertyName("password");
            $this->addMessage("Password has to contain at least one uppercase letter, one digit and has to be at least 8 characters long.");
        }

        $this->throwExceptionIfAnyError();
    }

    public static function isPasswordValid(string $password): bool
    {
        $pattern = '/^(?=.*[A-Z])(?=.*\d).{8,}$/';

        return preg_match($pattern, $password) === 1;
    }
}
