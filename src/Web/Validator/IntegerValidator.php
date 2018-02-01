<?php

namespace Jimdo\Reports\Web\Validator;

use Jimdo\Reports\ErrorCodeStore;

class IntegerValidator extends Validator
{
    /**
    * @param mixed $value
    * @return bool
    */
    public function isValid($value): bool
    {
        if (is_numeric($value) && strpos($value, '.') !== false) {
            $this->errorMessage = "'$value' is not an integer";
            $this->errorCode = ErrorCodeStore::ERR_VALIDATOR_INT;
            return false;
        }
        if (!is_numeric($value) || is_float($value)) {
            if (is_object($value)) {
                $value = get_class($value);
            }
            if (is_array($value)) {
                $value = 'Array';
            }
            $this->errorMessage = "'$value' is not an integer";
            $this->errorCode = ErrorCodeStore::ERR_VALIDATOR_INT;
            return false;
        }
        return true;
    }
}
