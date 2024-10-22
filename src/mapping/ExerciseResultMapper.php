<?php

namespace mapping;

use models\domain\exercise\ExerciseResult;
use models\domain\exercise\LanguageExerciseResult;
use models\request\CreateExerciseResultRequest;
use models\response\ExerciseResultResponse;
use models\response\VocabularyLanguageResponse;
use utils\Constants;

class ExerciseResultMapper
{
    public static function mapToExerciseResult(CreateExerciseResultRequest $request, int $userId): ExerciseResult
    {
        $answers = self::resolveCorrectAndIncorrectAnswers($request);

        $exerciseResult = new ExerciseResult();
        $exerciseResult->UserId = $userId;
        $exerciseResult->VocabularyLanguageId = $request->languageId;
        $exerciseResult->CorrectAnswers = $answers['correct'];
        $exerciseResult->IncorrectAnswers = $answers['incorrect'];

        return $exerciseResult;
    }

    public static function mapToExerciseResultResponse(LanguageExerciseResult $exerciseResult): ExerciseResultResponse
    {
        $response = new ExerciseResultResponse();

        $languageName = Constants::allLanguages()[$exerciseResult->VocabularyLanguageCode]['name'];

        $response->language = new VocabularyLanguageResponse();
        $response->language->id = $exerciseResult->VocabularyLanguageId;
        $response->language->code = $exerciseResult->VocabularyLanguageCode;
        $response->language->name = $languageName;
        $response->language->userId = $exerciseResult->UserId;
        $response->successRate = self::calculateSuccessRate($exerciseResult->CorrectAnswers, $exerciseResult->IncorrectAnswers);
        $totalAnswered = $exerciseResult->CorrectAnswers + $exerciseResult->IncorrectAnswers;
        $response->totalAnsweredWords = $totalAnswered;
        $response->answeredWordsAverage = $totalAnswered / $exerciseResult->ExercisesCount;

        return $response;
    }

    /**
     * @param \models\domain\exercise\LanguageExerciseResult[] $results
     */
    public static function mapToResultsResponse(array $results): array
    {
        $response = [];
        foreach ($results as $result) {
            $response[] = self::mapToExerciseResultResponse($result);
        }

        return $response;
    }

    public static function calculateSuccessRate(int $correctAnswers, int $incorrectAnswers): float
    {
        $totalAnswers = $correctAnswers + $incorrectAnswers;

        if ($totalAnswers === 0) {
            return 0;
        }

        return $correctAnswers / $totalAnswers * 100;
    }

    /**
     * @param CreateExerciseResultRequest $request
     * @return array array with two elements, first has key `correct` and value as a number of correct answers, 
     * second element has key `incorrect` and value as a number of incorrect answers
     */
    public static function resolveCorrectAndIncorrectAnswers(CreateExerciseResultRequest $request): array
    {
        $correctAnswers = 0;
        $incorrectAnswers = 0;
        foreach ($request->words as $word) {
            if ($word->isAnswredCorrectly) {
                $correctAnswers++;
            } else {
                $incorrectAnswers++;
            }
        }

        return ['correct' => $correctAnswers, 'incorrect' => $incorrectAnswers];
    }
}
