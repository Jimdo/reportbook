<?php

namespace Jimdo\Reports\Web\Validator;

use Jimdo\Reports\ErrorCodeStore;

class StringValidator extends Validator
{
    /**
    * @param mixed $value
    * @return bool
    */
    public function isValid($value): bool
    {
        if (!is_string($value)) {
            if (is_object($value)) {
                $value = get_class($value);
            }
            if (is_array($value)) {
                $value = 'Array';
            }
            $this->errorMessage = "'$value' is not a string";
            $this->errorCode = ErrorCodeStore::ERR_VALIDATOR_STRING;
            return false;
        }
        return true;
    }
}
