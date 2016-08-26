<?php

namespace Jimdo\Reports\Web\Validator;

class IntegerValidator implements Validator
{
    /** @var mixed */
    private $value;

    /**
    * @param mixed $value
    * @return bool
    */
    public function isValid($value): bool
    {
        $this->value = $value;
        if (!is_numeric($value) || is_float($value)) {
            return false;
        }
        return is_int((int) $value);
    }

    /**
    * @return string
    */
    public function errorMessage(): string
    {
        if (is_object($this->value)) {
            $this->value = get_class($this->value);
        }
        return "'$this->value' is not an integer";
    }
}
