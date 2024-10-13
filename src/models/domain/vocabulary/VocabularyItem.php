<?php

namespace models\domain\vocabulary;

use DateTime;
use models\domain\DomainEntity;
use utils\Constants;

class VocabularyItem extends DomainEntity
{
    public int $UserId;
    public int $VocabularyLanguageId;
    public string $Value;
    public string $Translations;
    public bool $IsLearned;
    public int $CorrectAnswers;
    private string $UpdatedAt;
    public function updatedAt(): DateTime
    {
        return new DateTime($this->UpdatedAt);
    }
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->UpdatedAt = $updatedAt->format(Constants::DATABASE_DATETIME_FORMAT);
    }
    public function databaseFormattedUpdatedAt(): string
    {
        return $this->UpdatedAt;
    }
}
