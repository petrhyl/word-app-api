<?php

namespace services\vocabulary;

use DateTime;
use Exception;
use mapping\VocabularyMapper;
use models\request\CreateVocabularyRequest;
use models\request\UpdateVocabularyItemTranslationsRequest;
use repository\language\LanguageRepository;
use repository\vocabulary\VocabularyRepository;
use services\language\LanguageService;
use services\user\auth\AuthService;
use WebApiCore\Exceptions\ApplicationException;

class VocabularyService
{
    public function __construct(
        private readonly VocabularyRepository $vocabularyRepository,
        private readonly LanguageService $languageService,
        private readonly LanguageRepository $languageRepository,
        private readonly AuthService $authService
    ) {}

    public function createVocabulary(CreateVocabularyRequest $request): void
    {
        $userId = $this->authService->getAuthenticatedUserId();

        $language = $this->languageRepository->getVocabularyLanguageById($request->languageId);

        if ($language === null || $language->UserId !== $userId) {
            throw new ApplicationException("User vocabulary language not found", 404);
        }

        $items = VocabularyMapper::mapCreateRequestToVocabularyItems($request, $userId);

        try {
            $result = $this->vocabularyRepository->createVocabulary($items);

            if ($result === false) {
                throw new Exception("Failed to create user's vocabulary", 101);
            }
        } catch (\Throwable $th) {
            if ($th->getCode() === 23000) {
                throw new ApplicationException("Provided word already exists in user's vocabulary", 409);
            } else {
                throw $th;
            }
        }
    }

    public function updateVocabularyItemTranslations(UpdateVocabularyItemTranslationsRequest $request, int $id): void
    {
        $existingItem = $this->vocabularyRepository->getVocabularyItem($id);

        if ($existingItem === null) {
            throw new ApplicationException("Vocabulary item not found", 404);
        }

        $userId = $this->authService->getAuthenticatedUserId();

        if ($existingItem->UserId !== $userId) {
            throw new ApplicationException("Vocabulary item not found", 404);
        }

        $language = $this->languageRepository->getVocabularyLanguageById($request->languageId);

        if ($language === null || $language->UserId !== $userId) {
            throw new ApplicationException("User vocabulary language not found", 404);
        }

        $item = $this->vocabularyRepository->getVocabularyItem($id);
        $item->Translations = implode(';', $request->translations);
        $item->setUpdatedAt(new DateTime());

        $result = $this->vocabularyRepository->updateVocabularyItem($item);

        if ($result === false) {
            throw new Exception("Failed to update vocabulary item", 101);
        }
    }
}
