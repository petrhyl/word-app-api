<?php

namespace mapping;

use models\domain\vocabulary\VocabularyItem;
use models\responces\UserVocabulary;

class VocabularyMapper
{
    public static function mapToUserVocabulary(VocabularyItem $word)
    {
        $userVocabulary = new UserVocabulary();
        $userVocabulary->id = $word->Id;
        $userVocabulary->word = $word->Value;
        $userVocabulary->language = $word->Language;
        $userVocabulary->updatedAt = $word->UpdatedAt;
        $userVocabulary->correctAnswers = $word->CorrectAnswers;
        $userVocabulary->isLearned = $word->IsLearned;
        $userVocabulary->translations = explode(',', $word->Translations);

        return $userVocabulary;
    }

    /**
     * @param VocabularyItem[] $words
     * @return UserVocabulary[]
     */
    public static function mapToUserVocabularyArray(array $words): array
    {
        $vocabulary = [];

        foreach ($words as $word) {
            $vocabulary[] = self::mapToUserVocabulary($word);
        }

        return $vocabulary;
    }
}
