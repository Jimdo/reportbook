<?php

namespace Jimdo\Reports\User\PasswordStrategy;

interface PasswordStrategy
{
    /**
     * @param string $password
     * @return string
     */
    public function encrypt(string $password): string;

    /**
    * @param string $password
    * @return bool
    */
    public function verify(string $password): bool;
}
