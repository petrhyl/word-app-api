<?php

namespace models\response;

class ExerciseResultResponse
{
    public int $userId;
    public int $vocabularyLanguageId;
    public float $successRate;
    public int $totalAnsweredWords;
    public int $answeredWordsAverage;
}