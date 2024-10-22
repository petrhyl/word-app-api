<?php

namespace validators\vocabulary;

use utils\Constants;
use validators\common\Validator;

class CreateVocabularyRequestValidator extends Validator{
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param \models\request\CreateVocabularyRequest $object
     */
    public function validate($object): void{
        if (empty($object->languageId)) {
            $this->addInvalidProperty("languageId", "Language ID can not be empty.");
        }

        if (empty($object->vocabularyItems)) {
            $this->addInvalidProperty("vocabularyItems", "Vocabulary items are not provided.");
        }

        $this->throwExceptionIfAnyError();

        foreach ($object->vocabularyItems as $vocabularyItem) {
            if (empty($vocabularyItem->word)) {
                $this->addInvalidProperty("word", "Word can not be empty.");
            }

            if (empty($vocabularyItem->translations)) {
                $this->addInvalidProperty("translations", "Translations of a word are not provided.");
            }

            if (count($vocabularyItem->translations) > 5) {
                $this->addInvalidProperty("translations", "Not allowed to have more than 5 translations to a single word.");
            }

            $this->throwExceptionIfAnyError();

            foreach ($vocabularyItem->translations as $translation) {
                if (empty($translation)) {
                    $this->addInvalidProperty("translations", "Translation can not be empty.");

                    $this->throwExceptionIfAnyError();
                }
            }
        }

        $this->throwExceptionIfAnyError();
    }
}