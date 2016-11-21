<?php

namespace Jimdo\Reports\User\PasswordStrategy;

class Hashed implements PasswordStrategy
{
    /**
     * @param string $password
     * @return string
     */
    public function encrypt(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
    * @param string $password1
    * @param string $hash
    * @return bool
    */
    public function verify(string $password1, string $hash): bool
    {
        return password_verify($password1, $hash);
    }
}
