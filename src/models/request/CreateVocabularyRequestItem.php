<?php

namespace models\request;

class CreateVocabularyRequestItem{
    public string $word;
    /**
     * @var string[]
     */
    public array $translations;
}