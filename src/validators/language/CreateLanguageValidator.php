<?php

namespace validators\language;

use utils\Constants;
use validators\common\Validator;

class CreateLanguageValidator extends Validator
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $object
     */
    public function validate($object): void
    {
        if (empty($object)) {
            $this->addInvalidProperty("code", "Language code parameter can not be empty.");
        }

        $allowedLanguages = Constants::allowedLanguages();
        if (!array_key_exists($object, $allowedLanguages)) {
            $this->addInvalidProperty("code", "Not applicable language alpha-2 code of language code parameter.");
        }

        $this->throwExceptionIfAnyError();
    }
}
