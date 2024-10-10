<?php

namespace validators\vocabulary;

use utils\Constants;
use validators\common\Validator;

class GetVocabularyQueryValidator extends Validator
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param \models\request\GetVocabularyQuery $object
     */
    public function validate($object): void{
        if (empty($object->lang)) {
            $this->addInvalidProperty("lang", "Language can not be empty.");
        }

        if (!array_key_exists($object->lang, Constants::languageCodes())) {
            $this->addInvalidProperty("lang", "Not applicable language alpha-2 code.");
        }

        if ($object->limit < 5 || $object->limit > 100) {
            $this->addInvalidProperty("limit", "Words' limit must be between 5 and 100.");
        }

        $this->throwExceptionIfAnyError();
    }
}