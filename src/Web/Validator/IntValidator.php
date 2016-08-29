<?php

namespace Jimdo\Reports\Web\Validator;

class IntValidator extends Validator
{
    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value): bool
    {
        if (!is_integer($value)) {
            if (is_object($value)) {
                $value = get_class($value);
            }
            $this->errorMessage = "'{$value} is not of type integer'";
            return false;
        }
        return true;
    }
}
