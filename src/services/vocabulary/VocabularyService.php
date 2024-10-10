<?php

namespace services\vocabulary;

use Exception;
use mapping\VocabularyMapper;
use models\request\CreateVocabularyRequest;
use models\request\GetVocabularyQuery;
use models\responces\UserVocabulary;
use repository\vocabulary\VocabularyRepository;
use services\user\auth\AuthService;
use utils\Utils;

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
        Utils::dd($request);
        $userId = $this->authService->getAuthenticatedUserId();
        
        $items = VocabularyMapper::mapToVocabularyItems($request, $userId);

        $result = $this->vocabularyRepository->createVocabulary($items);

        if ($result === false) {
            throw new Exception("Failed to create user's vocabulary");
        }
    }
}
