<?php

namespace Jimdo\Reports\Web\Validator;

class GreaterThanValidator extends Validator
{
    /**
     * @param int $greaterThan
     */
    public function __construct(int $greaterThan)
    {
        $this->greaterThan = $greaterThan;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value): bool
    {
        $intValidator = new IntValidator();
        $floatValidator = new FloatValidator();

        if (!($intValidator->isValid($value) || $floatValidator->isValid($value))
            || !($value > $this->greaterThan)) {
            $this->errorMessage = "'{$value}' is not greater than {$this->greaterThan}";
            return false;
        }
        return true;
    }
}
