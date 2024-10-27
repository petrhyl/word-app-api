<?php

namespace endpoints\languages;

use endpoints\BaseEndpoint;
use services\language\LanguageService;

class DeleteUserVocabularyLanguage extends BaseEndpoint
{
    public function __construct(private readonly LanguageService $languageService) {}

    public function __invoke(int $id)
    {
        $this->languageService->deleteUserVocabularyLanguage($id);

        $this->respondAndDie(null, 204);
    }
}
