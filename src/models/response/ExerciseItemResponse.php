<?php

namespace models\response;

class ExerciseItemResponse
{
    public int $id;
    public string $word;
    /**
     * @var string[]
     */
    public array $translations;
    public int $correctAnswers;
    public bool $isLearned;
    public string $updatedAt;
}