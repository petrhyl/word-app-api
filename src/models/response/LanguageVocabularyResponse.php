<?php

namespace models\response;

class LanguageVocabularyResponse{
    public LanguageResponse $language;
    /**
     * @var \models\response\VocabularyItemResponse[]
     */
    public array $items;
}