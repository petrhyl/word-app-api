<?php

namespace models\domain\exercise;

class LanguageExerciseResult
{
    public int $UserId;
    public int $VocabularyLanguageId;
    public int $CorrectAnswers;
    public int $IncorrectAnswers;
    public int $ExercisesCount;    
}