<?php

namespace endpoints\vocabulary;

use endpoints\BaseEndpoint;
use models\request\UpdateVocabularyItemTranslationsRequest;
use services\vocabulary\VocabularyService;

class UpdateUserVocabularyItemTranslations extends BaseEndpoint
{
    public function __construct(
        private readonly VocabularyService $vocabularyService
    ) {}

    public function __invoke(UpdateVocabularyItemTranslationsRequest $payload, $id): void
    {
        $this->vocabularyService->updateVocabularyItemTranslations($payload, $id);

        $this->respondAndDie(["message" => "Vocabulary item updated"]);
    }
}