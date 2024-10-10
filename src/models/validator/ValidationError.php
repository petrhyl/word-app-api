<?php

namespace models\validator;

class ValidationError{
    /**
     * @var string[]
     */
    public array $validationMessages = [];
    /**
     * @var InvalidPropertyError[]
     */
    public array $invalidProperties = [];
}