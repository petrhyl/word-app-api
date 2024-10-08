<?php

namespace services\vocabulary;

use VocabularyRepository;

class VocabularyService
{
    public function __construct(
        private readonly VocabularyRepository $vocabularyRepository
    ) {
    }
}