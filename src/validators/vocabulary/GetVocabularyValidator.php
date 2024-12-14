<?php

namespace validators\vocabulary;

use validators\common\Validator;
use validators\common\ValidatorUtils;

class GetVocabularyValidator extends Validator
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param mixed $object - language ID
     */
    public function validate($object): void
    {
        if (empty($object) || !ValidatorUtils::isInteger($object)) {
            $this->addMessage('Language ID must be an integer');
        }

        $this->throwExceptionIfAnyError();
    }
}
