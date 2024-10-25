<?php

namespace models\response;

class LanguageVocabularyResponse{
    public VocabularyLanguageResponse $language;
    /**
     * @var \models\response\VocabularyItemResponse[]
     */
    public array $items;
}