<?php

namespace validators\exercise;

use validators\common\Validator;

class CreateExerciseResultRequestValidator extends Validator
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param \models\request\CreateExerciseResultRequest $object
     */
    public function validate($object): void
    {
        if (empty($object->languageId)) {
            $this->addInvalidProperty("languageId", "Language ID can not be empty.");
        }

        if (empty($object->words)) {
            $this->addInvalidProperty("items", "Items can not be empty.");
        }

        $this->throwExceptionIfAnyError();

        foreach ($object->words as $item) {
            if (empty($item->id)) {
                $this->addInvalidProperty("id", "Item ID can not be empty.");
            }

            if (filter_var($item->id, FILTER_VALIDATE_INT) === false) {
                $this->addInvalidProperty("id", "Item ID must be an integer.");
            }

            $this->throwExceptionIfAnyError();
        }
    }
}
