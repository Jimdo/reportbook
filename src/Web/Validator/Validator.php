<?php

namespace Jimdo\Reports\Web\Validator;

abstract class Validator
{
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
