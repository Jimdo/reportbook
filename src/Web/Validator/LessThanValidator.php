<?php

namespace Jimdo\Reports\Web\Validator;

class LessThanValidator
{
    /**
     * @param int $lessThan
     */
    public function __construct(int $lessThan)
    {
        $this->lessThan = $lessThan;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $intValidator = new IntValidator();
        $floatValidator = new FloatValidator();

        return ($intValidator->isValid($value) || $floatValidator->isValid($value))
            && $value < $this->lessThan;
    }
}
