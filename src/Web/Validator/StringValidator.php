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
        return gettype($value) === 'string';
    }
}
