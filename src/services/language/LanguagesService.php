<?php

namespace services\language;

use mapping\LanguageMapper;
use repository\language\LanguageRepository;
use services\user\auth\AuthService;
use utils\Constants;

class LanguagesService
{
    public function __construct(
        private readonly LanguageRepository $languageRepository,
        private readonly AuthService $authService
    ) {}

    /**
     * @return string[]
     */
    public function getAllowedLanguages(): array
    {
        return array_values(Constants::allowedLanguages());
    }

    /**
     * @return \models\domain\language\UserLanguage[]
     */
    public function getUserLanguages(): array
    {
        $userId = $this->authService->getAuthenticatedUserId();

        $languages = $this->languageRepository->getUserLanguages($userId);

        return LanguageMapper::mapLanguagesToResponse($languages);
    }
}
