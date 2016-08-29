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
        if (!is_float($value)) {
            if (is_object($value)) {
                $value = get_class($value);
            }
            $this->errorMessage = "'{$value}' is no float";
            return false;
        }
        return true;
    }
}
