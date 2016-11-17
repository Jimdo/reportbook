<?php

namespace Jimdo\Reports\User;

class ClearTextPassword implements Password
{
    /** @var string */
    private $password;

    /**
     * @param string $password
     */
    public function __construct(string $password)
    {
        $this->password = $user->password();
    }

    /**
     * @param string $password
     * @return string
     */
    public function encrypt(string $password): string
    {

    }

    /**
     * @param string $password
     * @return string
     */
    public function decrypt(string $password): string
    {
        return $this->password;
    }
}
