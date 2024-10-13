<?php

namespace models\domain\language;

use models\domain\DomainEntity;

class VocabularyLanguage extends DomainEntity
{
    public int $UserId;
    public string $Code;
}
