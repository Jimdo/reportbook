<?php

namespace Jimdo\Reports\User\PasswordStrategy;

class ClearText implements PasswordStrategy
{
    /**
     * @param string $password
     * @return string
     */
    public function encrypt(string $password): string
    {
        return $password;
    }

    /**
    * @param string $password1
    * @param string $hash
    * @return bool
    */
    public function verify(string $password1, string $hash): bool
    {
        return $password1 === $hash;
    }
}
