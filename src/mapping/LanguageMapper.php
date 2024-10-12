<?php

namespace mapping;

use models\domain\language\UserLanguage;
use models\response\LanguageResponse;

class LanguageMapper
{
    public static function mapLanguageToResponse(UserLanguage $language): LanguageResponse
    {
        $response = new LanguageResponse();
        $response->id = $language->Id;
        $response->userId = $language->UserId;
        $response->code = $language->Code;
        $response->successRate = self::calculateSuccessRate($language->CorrectAnswers, $language->IncorrectAnswers);

        return $response;
    }

    /**
     * @param UserLanguage[] $languages
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

    private static function calculateSuccessRate(int $correctAnswers, int $incorrectAnswers): int
    {
        $totalAnswers = $correctAnswers + $incorrectAnswers;

        if ($totalAnswers === 0) {
            return 0;
        }

        return ($correctAnswers / $totalAnswers) * 100;
    }
}