<?php

namespace models\domain\vocabulary;

use DateTime;
use models\domain\DomainEntity;

class VocabularyItem extends DomainEntity{
    public int $UserId;
    public string $Value;
    public string $Translations;
    public string $Language;
    public bool $IsLearned;
    public int $CorrectAnswers;
    public DateTime $UpdatedAt;    
}