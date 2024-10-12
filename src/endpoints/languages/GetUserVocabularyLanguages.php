<?php

namespace endpoints\languages;

use endpoints\BaseEndpoint;
use services\language\LanguageService;

class GetUserVocabularyLanguages extends BaseEndpoint
{
    public function __construct(private readonly LanguageService $languageService)
    {
    }

    public function __invoke()
    {
        $response = $this->languageService->getUserVacabularyLanguages();

        $this->respondAndDie(["languages" => $response]);
    }
}