<?php

namespace models\domain\user;

use DateTime;
use models\domain\DomainEntity;
use utils\Constants;

class User extends DomainEntity
{
    public string $Email;
    public string $Name;
    public string $PasswordHash;
    public string $Language;
    public bool $IsVerified;
    public ?string $VerificationKey = null;

    /**
     * @var UserLogin[]|null
     */
    public ?array $Logins = [];
    public DateTime $UpdatedAt;
    
    public function mysqlFormattedUpdatedAt(): string
    {
        return $this->UpdatedAt->format(Constants::DATABASE_DATETIME_FORMAT);
    }
}
