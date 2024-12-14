<?php

namespace validators\vocabulary;

use validators\common\Validator;
use validators\common\ValidatorUtils;

class PagedQueryValidator extends Validator
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param \models\request\PagedQuery $object
     */
    public function validate($object): void
    {
        if (ValidatorUtils::isInteger($object->limit) === false) {
            $this->addInvalidProperty('limit', 'Limit must be an integer');
        }

        if (ValidatorUtils::isInteger($object->offset) === false) {
            $this->addInvalidProperty('offset', 'Offset must be an integer');
        }

        $this->throwExceptionIfAnyError();

        if ($object->limit < 1 || $object->limit > 200) {
            $this->addInvalidProperty('limit', 'Limit must be between 1 and 200');
        }

        if ($object->offset < 0) {
            $this->addInvalidProperty('offset', 'Offset must be greater than or equal to 0');
        }

        $this->throwExceptionIfAnyError();
    }
}
