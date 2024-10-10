<?php

namespace models\response;

class UserVocabulary
{
    public int $id;
    public string $word;
    /**
     * @var string[]
     */
    public array $translations;
    public int $correctAnswers;
    public int $isLearned;
    public string $language;
    public string $updatedAt;
}
