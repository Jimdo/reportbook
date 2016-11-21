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
    * @param string $password1
    * @param string $hash
    * @return bool
    */
    public abstract function verify(string $password1, string $hash): bool;
}
