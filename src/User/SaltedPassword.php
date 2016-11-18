<?php

namespace Jimdo\Reports\User;

class SaltedPassword implements Password
{
    /** @var string */
    private $hash;

    /**
     * @param string $password
     * @param string $hash
     */
    public function __construct(string $password, string $hash = null)
    {
        if ($hash === null) {
            $this->hash = $this->encrypt($password);
        } else {
            $this->hash = $hash;
        }
    }

    /**
     * @param string $password
     * @return string
     */
    public function encrypt(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * @param string $password
     * @return bool
     */
    public function verify(string $password): bool
    {
        return password_verify($password, $this->hash);
    }

    /**
     * @return string
     */
    public function password(): string
    {
        return $this->hash;
    }
}
