<?php

namespace services\exercise;

use DateTime;
use Exception;
use mapping\ExerciseResultMapper;
use mapping\VocabularyMapper;
use models\request\CreateExerciseResultRequest;
use models\request\GetExerciseQuery;
use models\response\ExerciseResponse;
use repository\exercise\ExerciseRepository;
use repository\language\LanguageRepository;
use repository\vocabulary\VocabularyRepository;
use services\user\auth\AuthService;

class ExerciseService{
    public function __construct(
        private readonly ExerciseRepository $exerciseRepository,
        private readonly VocabularyRepository $vocabularyRepository,
        private readonly LanguageRepository $languageRepository,
        private readonly AuthService $authService
    ) {}

    /**
     * @return \models\response\ExerciseResponse
     */
    public function getExercise(GetExerciseQuery $query): ExerciseResponse
    {
        $userId = $this->authService->getAuthenticatedUserId();

        $userLanguage = $this->languageRepository->getVocabularyLanguageById($query->langId);

        $words = $this->vocabularyRepository->getUserUnlearnedVocabulary($userId, $query->langId, $query->limit);
        $wordsCount = count($words);

        if ($wordsCount < $query->limit) {
            $learnedWords = $this->vocabularyRepository->getUserLearnedVocabulary($userId, $query->langId, $query->limit - $wordsCount);
            $words = array_merge($words, $learnedWords);
        }

        $exercise = VocabularyMapper::mapToExerciseResponse($words, $userLanguage);

        return $exercise;
    }

    /**
     * @return \models\response\ExerciseResultResponse[]
     */
    public function getUserResults() : array {
        $userId = $this->authService->getAuthenticatedUserId();

        $results = $this->exerciseRepository->getLanguageExerciseResultsOfUser($userId);

        return ExerciseResultMapper::mapToResultsResponse($results);
    }

    public function createExerciseResult(CreateExerciseResultRequest $request) : void{
        $userId = $this->authService->getAuthenticatedUserId();

        $exerciseResult =  ExerciseResultMapper::mapToExerciseResult($request, $userId);

        $result = $this->exerciseRepository->createResultAndUpdateVocabulary($exerciseResult, $request->words);

        if ($result === false) {
            throw new Exception("Failed to create exercise result", 101);
        }        
    }
}