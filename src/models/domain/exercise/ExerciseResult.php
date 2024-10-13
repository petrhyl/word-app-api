<?php

namespace models\domain\exercise;

use models\domain\DomainEntity;

class ExerciseResult extends DomainEntity
{
    public int $UserId;
    public int $VocabularyLanguageId;
    public int $CorrectAnswers;
    public int $IncorrectAnswers;
}