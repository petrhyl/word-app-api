<?php

namespace models\response;

class ExerciseResponse
{
    public int $languageId;
    public string $languageCode;
    /**
     * @var \models\response\ExerciseItemResponse[]
     */
    public array $words;
}
