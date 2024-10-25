<?php

namespace mapping;

use models\domain\language\VocabularyLanguage;
use models\response\LanguageResponse;
use models\response\VocabularyLanguageResponse;
use utils\Constants;

class LanguageMapper
{
    public static function mapVocabularyLanguageToResponse(VocabularyLanguage $language): VocabularyLanguageResponse
    {
        $response = new VocabularyLanguageResponse();
        $response->id = $language->Id;
        $response->userId = $language->UserId;
        $response->code = $language->Code;
        $response->name = Constants::allLanguages()[$language->Code]['name'];

        return $response;
    }

    /**
     * @param VocabularyLanguage[] $languages
     * @return VocabularyLanguageResponse[]
     */
    public static function mapVocabularyLanguagesToResponse(array $languages): array
    {
        $response = [];

        foreach ($languages as $language) {
            $response[] = self::mapVocabularyLanguageToResponse($language);
        }

        return $response;
    }

    /**
     * @param array $language array with keys 'code' and 'name'
     * @return \models\response\LanguageResponse
     */
    public static function mapToLanguageResponse(array $language): LanguageResponse
    {
        $response = new LanguageResponse();
        $response->code = $language['code'];
        $response->name = $language['name'];

        return $response;
    }

    /**
     * @param array $languages array of nested arrays with keys 'code' and 'name'
     * @return \models\response\LanguageResponse[]
     */
    public static function mapLanguagesToResponse(array $languages): array
    {
        $responses = [];
        foreach ($languages as $language) {
            $responses[] = self::mapToLanguageResponse($language);
        }

        return $responses;
    }
}
