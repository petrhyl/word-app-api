<?php

namespace models\request;

class CreateVocabularyRequest
{
    public string $language;
    /**
     * @var \models\request\CreateVocabularyRequestItem[]
     */
    public array $vocabularyItems;
}
