<?php

namespace models\domain;

use DateTime;
use utils\Constants;

abstract class DomainEntity
{
    public ?int $Id;
    protected ?string $CreatedAt;
    public function createdAt() : DateTime
    {
        return new DateTime($this->CreatedAt);        
    }
    public function setCreatedAt(DateTime $createdAt) : void
    {
        $this->CreatedAt = $createdAt->format(Constants::DATABASE_DATETIME_FORMAT);
    }
}
