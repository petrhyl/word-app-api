<?php

namespace endpoints\vocabularies;

use endpoints\BaseEndpoint;
use models\request\PagedQuery;
use services\vocabulary\VocabularyService;

class GetLanguageVocabulary extends BaseEndpoint{
    public function __construct(private readonly VocabularyService $vocabularyService) {
        
    }

    public function __invoke($langId, PagedQuery $query) {
        $response = $this->vocabularyService->getVocabularyOfLanguage($langId, $query);

        $this->respondAndDie(['vocabulary' => $response]);
    }
}