<?php

namespace validators\vocabulary;

use validators\common\Validator;

class CheckIfWordExistsValidator extends Validator
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param \models\request\CheckIfWordExistsRequest $object
     */
    public function validate($object): void
    {
        if (empty($object->word)) {
            $this->addInvalidProperty("word", "Word can not be empty.");
        }

        if (empty($object->languageId)) {
            $this->addInvalidProperty("languageId", "Language ID can not be empty.");
        }

        $this->throwExceptionIfAnyError();
    }
}