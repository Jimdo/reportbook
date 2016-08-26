<?php

namespace Jimdo\Reports\Web\Validator;

class FloatValidator
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
        return is_float($value);
    }

    /**
    * @return string
    */
    public function errorMessage(): string
    {
        if (is_object($this->value)) {
            $this->value = get_class($this->value);
        }
        return "'$this->value' is not a float";
    }
}
