<?php

namespace services\language;

use Exception;
use mapping\LanguageMapper;
use models\domain\language\UserLanguage;
use repository\language\LanguageRepository;
use services\user\auth\AuthService;
use utils\Constants;

class LanguageService
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
    public function getUserVacabularyLanguages(): array
    {
        $userId = $this->authService->getAuthenticatedUserId();

        $languages = $this->languageRepository->getVacabularyLanguagesOfUser($userId);

        return LanguageMapper::mapLanguagesToResponse($languages);
    }

    /**
     * @param string[] $languages
     * @param int $userId
     * @return void
     */
    public function createVocabularyLanguagesIfDoNotExist(array $languages, int $userId) : void {
        $userLanguages = $this->languageRepository->getVacabularyLanguagesOfUser($userId);

        $languageMap = [];
        foreach ($userLanguages as $userLang) {
            $languageMap[$userLang->Code] = $userLang->Code;
        }

        foreach ($languages as $lang) {
            if (!array_key_exists($lang, $languageMap)) {
                $languageMap[$lang] = $lang;

                $newUserLanguage = new UserLanguage();
                $newUserLanguage->Code = $lang;
                $newUserLanguage->UserId = $userId;
                $newUserLanguage->CorrectAnswers = 0;
                $newUserLanguage->IncorrectAnswers = 0;

                $result = $this->languageRepository->createVocabularyLanguage($newUserLanguage);

                if (!$result) {
                    throw new Exception("Failed to create user language [{$lang}]", 101);                    
                }
            }
        }
    }
}
