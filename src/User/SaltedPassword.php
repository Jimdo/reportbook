<?php

namespace Jimdo\Reports\User;

class SaltedPassword implements Password
{
    /**
     * @param string $password
     * @return string
     */
    public function encrypt(string $password): string
    {
        $salt = $this->generateSalt($password);
    }

    /**
     * @param string $password
     * @return string
     */
    public function decrypt(string $password): string
    {

    }

    /**
     * @param string $password
     * @return string
     */
    public function generateSalt(): string
    {
        return mcrypt_create_iv(22);
    }
}
