<?php

namespace Jimdo\Reports\User\PasswordStrategy;

class Hashed extends PasswordStrategy
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
    * @param string $password
    * @param string $hash
    * @return bool
    */
    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
