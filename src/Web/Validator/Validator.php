<?php

namespace Jimdo\Reports\Web\Validator;

abstract class Validator
{
    const ERR_VALIDATOR_BOOL = 1;
    const ERR_VALIDATOR_DATE = 2;
    const ERR_VALIDATOR_FLOAT = 3;
    const ERR_VALIDATOR_INT = 4;
    const ERR_VALIDATOR_STRING = 5;
    
    /** @var string */
    protected $errorMessage = '';

    /** @var int */
    protected $errorCode;

    /**
    * @param mixed $value
    * @return bool
    */
    abstract public function isValid($value): bool;

    /**
    * @return string
    */
    public function errorMessage(): string
    {
        return $this->errorMessage;
    }

    public function errorCode(): int
    {
        return $this->errorCode;
    }
}
