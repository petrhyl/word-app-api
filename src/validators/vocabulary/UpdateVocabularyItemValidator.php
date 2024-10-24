<?php

namespace validators\vocabulary;

use validators\common\Validator;

class UpdateVocabularyItemValidator extends Validator
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param \models\request\UpdateVocabularyItemRequest $object
     */
    public function validate($object): void
    {
        if (empty($object->languageId)) {
            $this->addInvalidProperty("languageId", "Language ID can not be empty.");
        }

        if (empty($object->word)) {
            $this->addInvalidProperty("word", "Word can not be empty.");
        }

        if (empty($object->translations)) {
            $this->addInvalidProperty("translations", "Translations of a word are not provided.");
        }

        if (count($object->translations) > 5) {
            $this->addInvalidProperty("translations", "Not allowed to have more than 5 translations to a single word.");
        }

        $this->throwExceptionIfAnyError();

        foreach ($object->translations as $translation) {
            if (empty($translation)) {
                $this->addInvalidProperty("translations", "Translation can not be empty.");

                $this->throwExceptionIfAnyError();
            }
        }
    }
}