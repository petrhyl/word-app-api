<?php

namespace mapping;

use models\domain\vocabulary\VocabularyItem;
use models\request\CreateVocabularyRequest;
use models\request\UpdateVocabularyItemRequest;
use models\response\UserVocabulary;

class VocabularyMapper
{
    public static function mapToUserVocabulary(VocabularyItem $word)
    {
        $userVocabulary = new UserVocabulary();
        $userVocabulary->id = $word->Id;
        $userVocabulary->word = $word->Value;
        $userVocabulary->language = $word->Language;
        $userVocabulary->updatedAt = $word->updatedAt()->format('c');
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

    public static function mapUpdateRequestToVocabularyItem(UpdateVocabularyItemRequest $request, int $id, int $userId) : VocabularyItem {
        $item = new VocabularyItem();
        $item->Id = $id;
        $item->UserId = $userId;
        $item->Value = $request->word;
        $item->Language = $request->language;
        $item->IsLearned = $request->isLearned;
        $item->CorrectAnswers = $request->correctAnswers;
        $item->Translations = implode(';', $request->translations);

        return $item;
    }
}
