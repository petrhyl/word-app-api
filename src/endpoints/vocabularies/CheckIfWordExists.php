<?php

namespace endpoints\vocabularies;

use endpoints\BaseEndpoint;
use models\request\CheckIfWordExistsRequest;
use services\vocabulary\VocabularyService;
use validators\vocabulary\CheckIfWordExistsValidator;

class CheckIfWordExists extends BaseEndpoint
{
    public function __construct(
        private readonly VocabularyService $vocabularyService,
        private readonly CheckIfWordExistsValidator $validator
    ) {}

    public function __invoke(CheckIfWordExistsRequest $payload)
    {
        $this->validator->validate($payload);

        $response = $this->vocabularyService->checkIfWordExists($payload);

        $this->respondAndDie(["exists" => $response]);
    }
}
