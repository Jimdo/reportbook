<?php

namespace Jimdo\Reports\Web\Validator;

use Jimdo\Reports\ErrorCodeStore;

class BoolValidator extends Validator
{
    /**
    * @param mixed $value
    * @return bool
    */
    public function isValid($value): bool
    {
        if ($value === 'true' || $value === 'false' || is_bool($value)) {
            return true;
        }
        if (is_object($value)) {
            $value = get_class($value);
        }
        if (is_array($value)) {
            $value = 'Array';
        }
        $this->errorMessage = "'$value' is not a bool";
        $this->errorCode = ErrorCodeStore::ERR_VALIDATOR_BOOL;
        return false;
    }
}
