<?php

namespace endpoints\languages;

use endpoints\BaseEndpoint;
use services\language\LanguageService;

class GetAllowedLanguages extends BaseEndpoint
{
    public function __construct(private readonly LanguageService $languageService) {}

    public function __invoke()
    {
        $response = $this->languageService->getAllowedLanguages();

        $this->respondAndDie(["languages" => $response]);
    }
}
