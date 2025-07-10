<?php

namespace validators\common;

use validators\common\models\InvalidPropertyError;
use validators\common\models\ValidationError;
use WebApiCore\Exceptions\ApplicationException;

abstract class Validator implements IValidator
{
    private ValidationError $error;


    protected function __construct()
    {
        $this->error = new ValidationError();
    }

    public function getErrors(): ValidationError
    {
        return $this->error;
    }

    public function throwExceptionIfAnyError(): void
    {
        if (!empty($this->error->invalidProperties) || !empty($this->error->validationMessages)) {
            throw new ApplicationException(
                "Invalid request structure's value(s).",
                422,
                100,
                ['validation' => [['properties' => $this->error->invalidProperties], ['messages' => $this->error->validationMessages]]]
            );
        }
    }

    protected function addMessage(string $message): void
    {
        $this->error->validationMessages[] = $message;
    }

    protected function addInvalidProperty(string $propertyName, string $errorDetails): void
    {
        $invalidProperty = new InvalidPropertyError();
        $invalidProperty->propertyName = $propertyName;
        $invalidProperty->details = $errorDetails;

        $this->error->invalidProperties[] = $invalidProperty;
    }
}
