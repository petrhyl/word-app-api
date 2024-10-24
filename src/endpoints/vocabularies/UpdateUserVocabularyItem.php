<?php

namespace endpoints\vocabularies;

use endpoints\BaseEndpoint;
use models\request\UpdateVocabularyItemRequest;
use services\vocabulary\VocabularyService;

class UpdateUserVocabularyItem extends BaseEndpoint
{
    public function __construct(
        private readonly VocabularyService $vocabularyService
    ) {}

    public function __invoke(UpdateVocabularyItemRequest $payload, $id): void
    {
        $this->vocabularyService->updateVocabularyItem($payload, $id);

        $this->respondAndDie(["message" => "Vocabulary item updated"]);
    }
}