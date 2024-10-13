<?php

namespace models\request;

class CreateExerciseResultRequest
{
    public int $languageId;
    /**
     * @var \models\request\ExerciseResultRequestItem[]
     */
    public array $words;
}
