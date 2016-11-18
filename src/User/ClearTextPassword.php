<?php

namespace Jimdo\Reports\User;

class ClearTextPassword implements Password
{
    /**
     * @param string $password
     * @return string
     */
    public function encrypt(string $password): string
    {

    }

    /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verify(string $password, string $hash): bool
    {

    }
}
