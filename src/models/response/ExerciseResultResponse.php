<?php

namespace models\response;

class ExerciseResultResponse
{
    public VocabularyLanguageResponse $language;
    public float $successRate;
    public int $totalAnsweredWords;
    public float $answeredWordsAverage;
}