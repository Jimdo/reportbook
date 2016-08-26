<?php

namespace Jimdo\Reports\Web\Validator;

class StringValidator implements Validator
{
    /** @var mixed */
    private $value;

    /** @var string */
    private $errorMessage = '';

    /**
    * @param mixed $value
    * @return bool
    */
    public function isValid($value): bool
    {
        $this->value = $value;
        return is_string($value);
    }

    /**
    * @return string
    */
    public function errorMessage(): string
    {
        if (is_object($this->value)) {
            $this->value = get_class($this->value);
            $this->errorMessage = "'$this->value' is not a string";
        }
        if (!is_string($this->value)) {
            $this->errorMessage = "'$this->value' is not a string";
        }
        return $this->errorMessage;
    }
}
