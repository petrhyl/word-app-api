<?php

namespace models\domain\language;

use models\domain\DomainEntity;

class UserLanguage extends DomainEntity
{
    public int $UserId;
    public string $Code;
    public int $CorrectAnswers;
    public int $IncorrectAnswers;
}
