<?php

namespace services\vocabulary;

use DateTime;
use Exception;
use mapping\VocabularyMapper;
use models\request\CreateVocabularyRequest;
use models\request\GetVocabularyQuery;
use models\request\UpdateVocabularyItemRequest;
use models\responces\UserVocabulary;
use repository\vocabulary\VocabularyRepository;
use services\user\auth\AuthService;
use WebApiCore\Exceptions\ApplicationException;

class VocabularyService
{
    public function __construct(
        private readonly VocabularyRepository $vocabularyRepository,
        private readonly AuthService $authService
    ) {}

    /**
     * @return UserVocabulary[]
     */
    public function getVocabulary(GetVocabularyQuery $query): array
    {
        $userId = $this->authService->getAuthenticatedUserId();

        $words = $this->vocabularyRepository->getUserUnlearnedVocabulary($userId, $query->lang, $query->limit);
        $wordsCount = count($words);

        if ($wordsCount < $query->limit) {
            $learnedWords = $this->vocabularyRepository->getUserLearnedVocabulary($userId, $query->lang, $query->limit - $wordsCount);
            $words = array_merge($words, $learnedWords);
        }

        $vocabulary = VocabularyMapper::mapToUserVocabularyArray($words);

        return $vocabulary;
    }

    public function createVocabulary(CreateVocabularyRequest $request): void
    {
        $userId = $this->authService->getAuthenticatedUserId();

        $items = VocabularyMapper::mapCreateRequestToVocabularyItems($request, $userId);

        try {
            $result = $this->vocabularyRepository->createVocabulary($items);
        } catch (\Throwable $th) {
            if ($th->getCode() === 23000) {
                throw new ApplicationException("Provided word already exists in user's vocabulary", 409);
            } else {
                throw $th;
            }
        }

        if ($result === false) {
            throw new Exception("Failed to create user's vocabulary");
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

        if ($existingItem->Value !== $request->word) {
            throw new ApplicationException("Not allowed to change value of vacabulary item.", 400);
        }

        $item = VocabularyMapper::mapUpdateRequestToVocabularyItem($request, $id, $userId);
        $item->setUpdatedAt(new DateTime());

        $result = $this->vocabularyRepository->updateVocabularyItem($item);

        if ($result === false) {
            throw new Exception("Failed to update vocabulary item");
        }
    }
}
