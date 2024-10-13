<?php

namespace models\request;

class UpdateVocabularyItemTranslationsRequest{
    public string $word;
    public int $languageId;
    /**
     * @var string[]
     */
    public array $translations;
}