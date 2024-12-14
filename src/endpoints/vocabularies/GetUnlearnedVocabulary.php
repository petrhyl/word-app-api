<?php

namespace endpoints\vocabularies;

use endpoints\BaseEndpoint;
use models\request\PagedQuery;
use services\vocabulary\VocabularyService;
use validators\vocabulary\GetVocabularyValidator;
use validators\vocabulary\PagedQueryValidator;

class GetUnlearnedVocabulary extends BaseEndpoint
{
    public function __construct(
        private readonly VocabularyService $vocabularyService,
        private readonly GetVocabularyValidator $validator,
        private readonly PagedQueryValidator $pagedQueryValidator
    ) {}

    public function __invoke($langId, PagedQuery $query)
    {
        $this->validator->validate($langId);
        $this->pagedQueryValidator->validate($query);

        $response = $this->vocabularyService->getUnlearnedVocabularyOfLanguage($langId, $query);

        $this->respondAndDie(['vocabulary' => $response]);
    }
}
