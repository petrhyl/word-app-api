<?php

namespace validators\common\models;

class ValidationError
{
    /**
     * @var string[]
     */
    public array $validationMessages = [];
    /**
     * @var InvalidPropertyError[]
     */
    public array $invalidProperties = [];
}
