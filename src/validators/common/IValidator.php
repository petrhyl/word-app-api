<?php

namespace validators\common;

use validators\common\models\ValidationError;

interface IValidator
{
    /**
     * @throws \WebApiCore\Exceptions\ApplicationException if the object is not valid
     */
    public function validate($object): void;

    public function getErrors(): ValidationError;
}
