<?php

namespace validators\user;

use utils\Constants;
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
            $this->addInvalidProperty("email", "Invalid e-mail format.");
        }

        if (empty($object->name)) {
            $this->addInvalidProperty("name", "Name can not be empty.");
        } elseif (!ValidatorUtils::isNameValid($object->name)) {
            $this->addInvalidProperty("name", "Name can only contain letters or hyphen, spaces, apostrophe.");
        }

        if (!ValidatorUtils::isPasswordValid($object->password)) {
            $this->addInvalidProperty("password", "Password has to contain at least one uppercase letter, one digit and has to be at least 8 characters long.");
        }

        if (!array_key_exists($object->language, Constants::allowedLanguages())) {
            $this->addInvalidProperty("language", "Invalid language value or format.");
            $allowedValues = implode(", ", array_values(Constants::allowedLanguages()));
            $this->addMessage("Allowed language values: {$allowedValues}");
        }

        $this->throwExceptionIfAnyError();
    }    
}
