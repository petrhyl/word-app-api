<?php

namespace endpoints\languages;

use endpoints\BaseEndpoint;
use services\language\LanguageService;
use validators\language\CreateLanguageValidator;

class CreateUserVocabularyLanguage extends BaseEndpoint
{
    public function __construct(
        private readonly LanguageService $languageService,
        private readonly CreateLanguageValidator $validator) {}

    public function __invoke($code)
    {
        $this->validator->validate($code);

        $response = $this->languageService->createUserVocabularyLanguage($code);

        $this->respondAndDie(['language' => $response], 201);
    }
}
