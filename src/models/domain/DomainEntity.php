<?php

namespace models\domain;

use DateTime;

abstract class DomainEntity
{
    public ?int $Id;
    public DateTime $CreatedAt;
}
