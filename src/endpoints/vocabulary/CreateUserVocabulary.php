<?php

namespace endpoints\vocabulary;

use endpoints\BaseEndpoint;
use models\request\CreateVocabularyRequest;
use services\vocabulary\VocabularyService;
use utils\Utils;
use validators\vocabulary\CreateVocabularyRequestValidator;

class CreateUserVocabulary extends BaseEndpoint
{
    public function __construct(
        private readonly VocabularyService $vocabularyService,
        private readonly CreateVocabularyRequestValidator $validator) {}

    public function __invoke(CreateVocabularyRequest $payload)
    {             
        $this->validator->validate($payload);

        $this->vocabularyService->createVocabulary($payload);

        $this->respondAndDie(['message' => 'Vocabulary was successfully created.'], 201);
    }
}
