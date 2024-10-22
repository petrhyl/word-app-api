<?php

namespace models\response;

class ExerciseResponse
{
    public int $languageId;
    public string $languageCode;
    public string $languageName;
    /**
     * @var \models\response\ExerciseItemResponse[]
     */
    public array $words;
}
