<?php

namespace models\request;

class CreateVocabularyRequest
{
    public int $languageId;
    /**
     * @var \models\request\CreateVocabularyRequestItem[]
     */
    public array $vocabularyItems;
}
