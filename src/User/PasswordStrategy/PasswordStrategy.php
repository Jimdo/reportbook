<?php

namespace Jimdo\Reports\User\PasswordStrategy;

abstract class PasswordStrategy
{
    /**
     * @param string $password
     * @return string
     */
    public abstract function encrypt(string $password): string;

    /**
    * @param string $password
    * @param string $hash
    * @return bool
    */
    public abstract function verify(string $password, string $hash): bool;
}
