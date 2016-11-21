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
    * @param string $password1
    * @param string $password2
    * @return bool
    */
    public function verify(string $password1, string $password2): bool;
}
