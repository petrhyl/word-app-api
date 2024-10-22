<?php

namespace mapping;

use models\domain\language\VocabularyLanguage;
use models\domain\vocabulary\VocabularyItem;
use models\request\CreateVocabularyRequest;
use models\response\ExerciseItemResponse;
use models\response\ExerciseResponse;

class VocabularyMapper
{
    public static function mapToExerciseItemResponse(VocabularyItem $word)
    {
        $item = new ExerciseItemResponse();
        $item->id = $word->Id;
        $item->word = $word->Value;
        $item->updatedAt = $word->updatedAt()->format('c');
        $item->correctAnswers = $word->CorrectAnswers;
        $item->isLearned = $word->IsLearned;
        $item->translations = explode(';', $word->Translations);

        return $item;
    }

    /**
     * @param VocabularyItem[] $vocabularyItems
     * @return \models\response\ExerciseResponse
     */
    public static function mapToExerciseResponse(array $vocabularyItems, VocabularyLanguage $language): ExerciseResponse
    {
        $words = [];

        foreach ($vocabularyItems as $item) {
            $words[] = self::mapToExerciseItemResponse($item);
        }

        $exercise = new ExerciseResponse();
        $exercise->languageId = $language->Id;
        $exercise->languageCode = $language->Code;

        $exercise->words = $words;

        return $exercise;
    }

    /**
     * @param CreateVocabularyRequest $request
     * @return VocabularyItem[]
     */
    public static function mapCreateRequestToVocabularyItems(CreateVocabularyRequest $request, int $userId): array
    {
        $items = [];
        foreach ($request->vocabularyItems as $requestItem) {
            $item = new VocabularyItem();
            $item->UserId = $userId;
            $item->Value = $requestItem->word;
            $item->VocabularyLanguageId = $request->languageId;
            $item->IsLearned = false;
            $item->CorrectAnswers = 0;
            $item->Translations = implode(';', $requestItem->translations);
            $items[] = $item;
        }

        return $items;
    }
}
