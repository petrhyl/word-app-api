<?php

namespace validators\user;

use utils\Constants;
use validators\common\Validator;
use validators\common\ValidatorUtils;

class UpdateUserValidator extends Validator{
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param \models\request\UpdateUserRequest $object
     */
    public function validate($object): void
    {
        if (empty($object->name)) {
            $this->addInvalidProperty("name", "Name can not be empty.");
        } elseif (!ValidatorUtils::isNameValid($object->name)) {
            $this->addInvalidProperty("name", "Name can only contain letters or hyphen, spaces, apostrophe.");
        }

        if (!array_key_exists($object->language, Constants::allowedLanguages())) {
            $this->addInvalidProperty("language", "Invalid language value or format.");
            $allowedValues = implode(", ", array_values(Constants::allowedLanguages()));
            $this->addMessage("Allowed language values: {$allowedValues}");
        }

        $this->throwExceptionIfAnyError();
    }
}