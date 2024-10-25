<?php

namespace mapping;

use models\domain\language\VocabularyLanguage;
use models\domain\vocabulary\VocabularyItem;
use models\request\CreateVocabularyRequest;
use models\request\UpdateVocabularyItemRequest;
use models\response\VocabularyItemResponse;
use models\response\ExerciseResponse;
use models\response\LanguageVocabularyResponse;
use utils\Constants;

class VocabularyMapper
{
    public static function mapToVocabularyItemResponse(VocabularyItem $word)
    {
        $item = new VocabularyItemResponse();
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
            $words[] = self::mapToVocabularyItemResponse($item);
        }

        $languageName = Constants::allLanguages()[$language->Code]['name'];

        $exercise = new ExerciseResponse();
        $exercise->languageId = $language->Id;
        $exercise->languageCode = $language->Code;
        $exercise->languageName = $languageName;

        $exercise->words = $words;

        return $exercise;
    }

    public static function mapToLanguageVocabularyResponse(VocabularyLanguage $language, array $items) : LanguageVocabularyResponse
    {
        $response = new LanguageVocabularyResponse();

        $languageAssocArray = Constants::allLanguages()[$language->Code];

        $response->language = LanguageMapper::mapToLanguageResponse($languageAssocArray);
        $response->items = array_map(fn($item) => self::mapToVocabularyItemResponse($item), $items);

        return $response;        
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

    public static function mapUpdateRequestToVocabularyItem(UpdateVocabularyItemRequest $request, VocabularyItem $existingItem): VocabularyItem
    {
        $item = new VocabularyItem();
        $item->Id = $existingItem->Id;
        $item->UserId = $existingItem->UserId;
        $item->Value = $request->word;
        $item->VocabularyLanguageId = $existingItem->VocabularyLanguageId;
        $item->IsLearned = $existingItem->IsLearned;
        $item->CorrectAnswers = $existingItem->CorrectAnswers;
        $item->Translations = implode(';', $request->translations);

        return $item;
    }
}
