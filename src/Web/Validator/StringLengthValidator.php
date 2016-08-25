<?php

namespace Jimdo\Reports\Web\Validator;

class StringLengthValidator extends Validator
{
    /**
     * @param int $length
     */
    public function __construct(int $length)
    {
        $this->length = $length;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value): bool
    {
        if (!(new StringValidator())->isValid($value) && strlen($value) === $this->length) {
            $this->errorMessage = "'{$value}' does not match length of {$this->length}";
            return false;
        }
        return true;
    }
}
