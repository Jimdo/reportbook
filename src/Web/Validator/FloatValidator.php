<?php

namespace Jimdo\Reports\Web\Validator;

class FloatValidator extends Validator
{
    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value): bool
    {
        if (!gettype($value) === 'double') {
            $this->errorMessage = "'{$value}' is no float";
            return false;
        }
        return true;
    }
}
