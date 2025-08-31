<?php

namespace models\domain\user;

use DateTime;
use models\domain\DomainEntity;
use utils\Constants;

class UserLogin extends DomainEntity
{
    public int $UserId;
    public string $TokenHash;

    private string $ExpiresIn;
    public function setExpiresIn(DateTime $dateTime): void
    {
        $this->ExpiresIn = $dateTime->format(Constants::DATABASE_DATETIME_FORMAT);
    }
    public function expiresAt(): DateTime
    {
        return DateTime::createFromFormat(Constants::DATABASE_DATETIME_FORMAT, $this->ExpiresIn);
    }
}
