<?php

namespace Jimdo\Reports\User;

interface Password
{
    /**
     * @param string $password
     * @return string
     */
    public function encrypt(string $password): string

    /**
     * @param string $password
     * @return string
     */
    public function decrypt(string $password): string
}
