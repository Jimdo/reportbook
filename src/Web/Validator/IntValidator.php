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
        if (!gettype($value) === 'integer') {
            $this->errorMessage = "'{$value} is not of type integer'";
            return false;
        }
        return true;
    }
}
