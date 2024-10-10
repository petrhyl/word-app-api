<?php

namespace validators\user;

use validators\common\Validator;

class VerificationKeyValidator extends Validator
{
    private const HASH_LENGTH = 64;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $object varification key
     */
    public function validate($object): void
    {
        if (empty($object) || strlen($object) !== self::HASH_LENGTH) {
            $this->addInvalidProperty('verificationKey', 'Invalid verification key value.');
        }

        $this->throwExceptionIfAnyError();
    }
}
