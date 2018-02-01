<?php

namespace Jimdo\Reports\Web\Validator;

use Jimdo\Reports\ErrorCodeStore;

class FloatValidator extends Validator
{
    /**
    * @param mixed $value
    * @return bool
    */
    public function isValid($value): bool
    {
        if (!is_numeric($value) || is_int($value)) {
            if (is_object($value)) {
                $value = get_class($value);
            }
            if (is_array($value)) {
                $value = 'Array';
            }
            $this->errorMessage = "'$value' is not a float";
            $this->errorCode = ErrorCodeStore::ERR_VALIDATOR_FLOAT;
            return false;
        }
        return true;
    }
}
