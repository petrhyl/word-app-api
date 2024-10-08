<?php

namespace validators\common;

interface IValidator
{
    /**
     * @throws \WebApiCore\Exceptions\ApplicationException if the object is not valid
     */
    public function validate($object): void;

    public function getErrors(): array;
}
