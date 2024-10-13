<?php

namespace mapping;

use models\domain\language\VocabularyLanguage;
use models\response\LanguageResponse;

class LanguageMapper
{
    public static function mapLanguageToResponse(VocabularyLanguage $language): LanguageResponse
    {
        $response = new LanguageResponse();
        $response->id = $language->Id;
        $response->userId = $language->UserId;
        $response->code = $language->Code;

        return $response;
    }

    /**
     * @param VocabularyLanguage[] $languages
     * @return LanguageResponse[]
     */
    public static function mapLanguagesToResponse(array $languages): array
    {
        $response = [];

        foreach ($languages as $language) {
            $response[] = self::mapLanguageToResponse($language);
        }

        return $response;
    }    
}