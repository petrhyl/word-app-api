<?php

namespace services\language;

use Exception;
use mapping\LanguageMapper;
use models\domain\language\VocabularyLanguage;
use models\response\VocabularyLanguageResponse;
use repository\language\LanguageRepository;
use services\user\auth\AuthService;
use utils\Constants;
use WebApiCore\Exceptions\ApplicationException;

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
        return LanguageMapper::mapLanguagesToResponse(Constants::allowedLanguages());
    }

    /**
     * @return \models\response\VocabularyLanguageResponse[]
     */
    public function getUserVacabularyLanguages(): array
    {
        $userId = $this->authService->getAuthenticatedUserId();

        $languages = $this->languageRepository->getVacabularyLanguagesOfUser($userId);

        return LanguageMapper::mapVocabularyLanguagesToResponse($languages);
    }

    public function createUserVocabularyLanguage(string $languageCode) : VocabularyLanguageResponse {
        $userId = $this->authService->getAuthenticatedUserId();

        $language = $this->getUserVocabularyLanguageOrCreateIfDoesNotExist($languageCode, $userId);

        return LanguageMapper::mapVocabularyLanguageToResponse($language);
    }

    /**
     * @param string $language
     * @param int $userId
     * @return VocabularyLanguage returns created vocabulary language or existing one
     * @throws ApplicationException
     * @throws Exception
     */
    public function getUserVocabularyLanguageOrCreateIfDoesNotExist(string $language, int $userId): VocabularyLanguage
    {
        $userLanguages = $this->languageRepository->getVacabularyLanguagesOfUser($userId);        

        foreach ($userLanguages as $lang) {
            if ($lang->Code === $language) {
                return $lang;
            }
        }

        if (count($userLanguages) > 14) {
            throw new ApplicationException("User can have up to 15 languages", 422);
        }

        $userLanguage = new VocabularyLanguage();
        $userLanguage->Code = $language;
        $userLanguage->UserId = $userId;

        $result = $this->languageRepository->createVocabularyLanguage($userLanguage);

        if (!$result) {
            throw new Exception("Failed to create vocabulary language", 101);
        }

        return $result;
    }
}
