<?php

namespace validators\common;

use WebApiCore\Exceptions\ApplicationException;

abstract class Validator implements IValidator
{
    protected array $errors;

    protected const MSG_KEY = "validationMessage";
    protected const PROP_NAME = "invalidProperties";

    protected function __construct()
    {
        $this->errors[self::MSG_KEY] = "";
        $this->errors[self::PROP_NAME] = "";
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function throwExceptionIfAnyError(): void
    {
        if (empty($this->errors[self::MSG_KEY]) && empty($this->errors[self::PROP_NAME]) && count($this->errors) < 3) {
            return;
        }

        throw new ApplicationException("Invalid request body value(s).", 422, 100, $this->errors);
    }

    protected function addMessage(string $message): void
    {
        if (!empty($this->errors[self::MSG_KEY])) {
            $this->errors[self::MSG_KEY] .= " ";
        }

        $this->errors[self::MSG_KEY] .= $message;
    }

    protected function addInvalidPropertyName(string $propertyName): void
    {
        if (!empty($this->errors[self::PROP_NAME])) {
            $this->errors[self::PROP_NAME] .= ", ";
        }

        $this->errors[self::PROP_NAME] .= $propertyName;
    }
}
