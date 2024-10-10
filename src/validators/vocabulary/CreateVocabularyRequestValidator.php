<?php

namespace validators\vocabulary;

use utils\Constants;
use utils\Utils;
use validators\common\Validator;

class CreateVocabularyRequestValidator extends Validator{
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param \models\request\CreateVocabularyRequest $object
     */
    public function validate($object): void{
        if (empty($object->language)) {
            $this->addInvalidProperty("language", "Language can not be empty.");
        }

        if (!array_key_exists($object->language, Constants::languageCodes())) {
            $this->addInvalidProperty("language", "Not applicable language alpha-2 code.");
        }

        if (empty($object->vocabularyItems)) {
            $this->addInvalidProperty("vocabularyItems", "Vocabulary items are not provided.");
        }

        foreach ($object->vocabularyItems as $vocabularyItem) {
            if (empty($vocabularyItem->word)) {
                $this->addInvalidProperty("word", "Word can not be empty.");
            }

            if (empty($vocabularyItem->translations) || $vocabularyItem->translations[0] === "") {
                $this->addInvalidProperty("translations", "Translations of a word are not provided.");
            }

            if (count($vocabularyItem->translations) > 5) {
                $this->addInvalidProperty("translations", "Not allowed to have more than 5 translations to a single word.");
            }

            $this->throwExceptionIfAnyError();
        }

        $this->throwExceptionIfAnyError();
    }
}