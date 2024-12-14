<?php

namespace services\vocabulary;

use DateTime;
use Exception;
use mapping\VocabularyMapper;
use models\request\CheckIfWordExistsRequest;
use models\request\CreateVocabularyRequest;
use models\request\PagedQuery;
use models\request\UpdateVocabularyItemRequest;
use models\response\LanguageVocabularyResponse;
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

    public function getVocabularyOfLanguage(int $langId, PagedQuery $paging): LanguageVocabularyResponse
    {
        $userId = $this->authService->getAuthenticatedUserId();

        $language = $this->languageRepository->getVocabularyLanguageById($langId);

        if ($language === null || $language->UserId !== $userId) {
            throw new ApplicationException("User's vocabulary language not found", 404);
        }

        $items = $this->vocabularyRepository->getUserVocabulary($userId, $langId, $paging->limit, $paging->offset);

        return VocabularyMapper::mapToLanguageVocabularyResponse($language, $items);
    }

    public function getUnlearnedVocabularyOfLanguage(int $langId, PagedQuery $paging): LanguageVocabularyResponse
    {
        $userId = $this->authService->getAuthenticatedUserId();

        $language = $this->languageRepository->getVocabularyLanguageById($langId);

        if ($language === null || $language->UserId !== $userId) {
            throw new ApplicationException("User's vocabulary language not found", 404);
        }

        $items = $this->vocabularyRepository->getUserUnlearnedVocabulary($userId, $langId, $paging->limit, $paging->offset);

        return VocabularyMapper::mapToLanguageVocabularyResponse($language, $items);
    }

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
            if (strval($th->getCode()) === "23000") {
                $existingWord = null;
                $existingWordErrorPattern = "/Duplicate entry '(\d+)-(\w+)-(\d+)' for key/u";

                if (preg_match($existingWordErrorPattern, $th->getMessage(), $matches)) {
                    $existingWord = $matches[2];
                }

                throw new ApplicationException(
                    "Provided word already exists in user's vocabulary",
                    409,
                    100,
                    ["existingWord" => $existingWord ?? ""]
                );
            } else {
                throw $th;
            }
        }
    }

    public function updateVocabularyItem(UpdateVocabularyItemRequest $request, int $id): void
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

        $item = VocabularyMapper::mapUpdateRequestToVocabularyItem($request, $existingItem);
        $item->CorrectAnswers = 0;
        $item->IsLearned = null;
        $item->setUpdatedAt(new DateTime());

        $result = $this->vocabularyRepository->updateVocabularyItem($item);

        if ($result === false) {
            throw new Exception("Failed to update vocabulary item", 101);
        }
    }

    public function checkIfWordExists(CheckIfWordExistsRequest $request): bool
    {
        $userId = $this->authService->getAuthenticatedUserId();

        $result = $this->vocabularyRepository->getUserVocabularyItem($userId, $request->languageId, $request->word);

        if ($result === null) {
            return false;
        }

        return true;
    }
}
