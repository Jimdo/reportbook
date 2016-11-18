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
        $this->password = $password;
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
     * @return bool
     */
    public function verify(string $password): bool
    {
        return ($password === $this->password);
    }

    /**
     * @return string
     */
    public function password(): string
    {
        return $this->password;
    }
}
