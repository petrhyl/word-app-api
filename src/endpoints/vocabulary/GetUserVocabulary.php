<?php

namespace endpoints\vocabulary;

use endpoints\BaseEndpoint;
use models\request\GetVocabularyQuery;
use services\vocabulary\VocabularyService;
use validators\vocabulary\GetVocabularyQueryValidator;

class GetUserVocabulary extends BaseEndpoint
{
    public function __construct(
        private readonly VocabularyService $vocabularyService,
        private readonly GetVocabularyQueryValidator $validator
    ) {}

    public function __invoke(GetVocabularyQuery $query)
    {
        $this->validator->validate($query);

        $response = $this->vocabularyService->getVocabulary($query);

        $this->respondAndDie(['vacabulary' => $response]);
    }
}
