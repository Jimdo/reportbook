<?php

namespace Jimdo\Reports\Web\Validator;

class IntegerValidator implements Validator
{
    /** @var string */
    private $errorMessage = '';

    /**
    * @param mixed $value
    * @return bool
    */
    public function isValid($value): bool
    {
        if (!is_numeric($value) || is_float($value)) {
            if (is_object($value)) {
                $value = get_class($value);
            }
            if (is_array($value)) {
                $value = 'Array';
            }
            $this->errorMessage = "'$value' is not an integer";
            return false;
        }
        return true;
    }

    /**
    * @return string
    */
    public function errorMessage(): string
    {
        return $this->errorMessage;
    }
}
