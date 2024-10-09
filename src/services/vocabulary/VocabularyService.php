<?php

namespace services\vocabulary;

use mapping\VocabularyMapper;
use models\requests\GetVocabularyRequest;
use models\responces\UserVocabulary;
use repository\vocabulary\VocabularyRepository;
use services\user\auth\AuthService;

class VocabularyService
{
    public function __construct(
        private readonly VocabularyRepository $vocabularyRepository,
        private readonly AuthService $authService
    ) {}

    /**
     * @return UserVocabulary[]
     */
    public function getVocabulary(GetVocabularyRequest $request): array
    {
        $userId = $this->authService->getAuthenticatedUserId();

        $words = $this->vocabularyRepository->getUserUnlearnedVocabulary($userId, $request->language, $request->limit);
        $wordsCount = count($words);

        if ($wordsCount < $request->limit) {
            $learnedWords = $this->vocabularyRepository->getUserLearnedVocabulary($userId, $request->language, $request->limit - $wordsCount);
            $words = array_merge($words, $learnedWords);
        }

        $vocabulary = VocabularyMapper::mapToUserVocabularyArray($words);

        return $vocabulary;
    }
}
