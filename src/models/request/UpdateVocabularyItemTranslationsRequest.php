<?php

namespace models\request;

class UpdateVocabularyItemRequest{
    public string $word;
    public int $languageId;
    /**
     * @var string[]
     */
    public array $translations;
}