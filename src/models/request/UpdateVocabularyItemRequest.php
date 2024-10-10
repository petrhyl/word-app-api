<?php

namespace models\request;

class UpdateVocabularyItemRequest{
    public string $word;
    public string $language;
    public bool $isLearned;
    public int $correctAnswers;
    /**
     * @var string[]
     */
    public array $translations;
}