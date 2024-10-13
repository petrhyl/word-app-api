<?php

namespace validators\exercise;

use validators\common\Validator;

class GetExerciseQueryValidator extends Validator
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param \models\request\GetExerciseQuery $object
     */
    public function validate($object): void{
        if (empty($object->langId)) {
            $this->addInvalidProperty("langId", "Language ID can not be empty.");
        }

        if (filter_var($object->langId, FILTER_VALIDATE_INT) === false) {
            $this->addInvalidProperty("langId", "Language ID must be a integer.");
        }

        if ($object->limit < 5 || $object->limit > 100) {
            $this->addInvalidProperty("limit", "Words' limit must be between 5 and 100.");
        }

        $this->throwExceptionIfAnyError();
    }
}