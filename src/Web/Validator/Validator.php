<?php

namespace Jimdo\Reports\Web\Validator;

interface Validator
{
    /**
    * @param mixed $value
    * @return bool
    */
    public function isValid($value): bool;

    /**
    * @return string
    */
    public function errorMessage(): string;
}
