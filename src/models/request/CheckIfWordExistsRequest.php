<?php

namespace models\request;

class CheckIfWordExistsRequest
{
    public int $languageId;
    public string $word;
}