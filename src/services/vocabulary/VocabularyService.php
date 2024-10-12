<?php

namespace services\vocabulary;

use DateTime;
use Exception;
use mapping\VocabularyMapper;
use models\domain\language\UserLanguage;
use models\domain\vocabulary\VocabularyItem;
use models\request\CreateVocabularyRequest;
use models\request\GetVocabularyQuery;
use models\request\UpdateVocabularyItemRequest;
use models\responces\UserVocabulary;
use repository\language\LanguageRepository;
use repository\vocabulary\VocabularyRepository;
use services\user\auth\AuthService;
use WebApiCore\Exceptions\ApplicationException;

class VocabularyService
{
    public function __construct(
        private readonly VocabularyRepository $vocabularyRepository,
        private readonly LanguageRepository $languageRepository,
        private readonly AuthService $authService
    ) {}

    /**
     * @return UserVocabulary[]
     */
    public function getVocabulary(GetVocabularyQuery $query): array
    {
        $userId = $this->authService->getAuthenticatedUserId();

        $words = $this->vocabularyRepository->getUserUnlearnedVocabulary($userId, $query->lang, $query->limit);
        $wordsCount = count($words);

        if ($wordsCount < $query->limit) {
            $learnedWords = $this->vocabularyRepository->getUserLearnedVocabulary($userId, $query->lang, $query->limit - $wordsCount);
            $words = array_merge($words, $learnedWords);
        }

        $vocabulary = VocabularyMapper::mapToUserVocabularyArray($words);

        return $vocabulary;
    }

    public function createVocabulary(CreateVocabularyRequest $request): void
    {
        $userId = $this->authService->getAuthenticatedUserId();

        $items = [];
        $userLanguages = $this->languageRepository->getVacabularyLanguagesOfUser($userId);
        $languages = [];

        foreach ($userLanguages as $lang) {
            $languages[$lang->Code] = $lang->Code;
        }       

        foreach ($request->vocabularyItems as $requestItem) {
            $item = new VocabularyItem();
            $item->UserId = $userId;
            $item->Value = $requestItem->word;
            $item->Language = $request->language;
            $item->IsLearned = false;
            $item->CorrectAnswers = 0;
            $item->Translations = implode(';', $requestItem->translations);

            $items[] = $item;

            if (!array_key_exists($item->Language, $languages)) {
                $languages[$item->Language] = $item->Language;

                $userLanguage = new UserLanguage();
                $userLanguage->Code = $item->Language;
                $userLanguage->UserId = $userId;
                $userLanguage->CorrectAnswers = 0;
                $userLanguage->IncorrectAnswers = 0;

                $this->languageRepository->createVocabularyLanguage($userLanguage);
            }
        } 
        
        try {
            $result = $this->vocabularyRepository->createVocabulary($items);
        } catch (\Throwable $th) {
            if ($th->getCode() === 23000) {
                throw new ApplicationException("Provided word already exists in user's vocabulary", 409);
            } else {
                throw $th;
            }
        }

        if ($result === false) {
            throw new Exception("Failed to create user's vocabulary");
        }
    }

    public function updateVocabularyItem(UpdateVocabularyItemRequest $request, int $id): void
    {        
        $existingItem = $this->vocabularyRepository->getVocabularyItem($id);
        
        if ($existingItem === null) {
            throw new ApplicationException("Vocabulary item not found", 404);
        }
        
        $userId = $this->authService->getAuthenticatedUserId();

        if ($existingItem->UserId !== $userId) {
            throw new ApplicationException("Vocabulary item not found", 404);
        }

        if ($existingItem->Value !== $request->word) {
            throw new ApplicationException("Not allowed to change value of vacabulary item.", 400);
        }

        $item = VocabularyMapper::mapUpdateRequestToVocabularyItem($request, $id, $userId);
        $item->setUpdatedAt(new DateTime());

        $result = $this->vocabularyRepository->updateVocabularyItem($item);

        if ($result === false) {
            throw new Exception("Failed to update vocabulary item");
        }
    }
}
