<?php

namespace Jimdo\Reports\Web\Validator;

class StringValidator
{
    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            if (is_object($value)) {
                $value = get_class($value);
            }
            $this->errorMessage = "'{$value} is not of type string'";
            return false;
        }
        return true;
    }
}
