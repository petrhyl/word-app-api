<?php

namespace models\domain\user;

use DateTime;
use models\domain\DomainEntity;
use utils\Constants;

class User extends DomainEntity
{
    public string $Email;
    public string $Name;
    public ?string $PasswordHash = null;
    public string $Language;
    public bool $IsVerified;
    public ?string $VerificationKey = null;
    public ?AuthToken $AccessToken = null;
    public ?AuthToken $RefreshToken = null;
    public DateTime $UpdatedAt;
    
    public function mysqlFormattedUpdatedAt(): string
    {
        return $this->UpdatedAt->format(Constants::MYSQL_DATETIME_FORMAT);
    }
}
