<?php

namespace Jimdo\Reports\User\PasswordConstraints;

abstract class PasswordConstraintsFactory
{
    /**
     * @param string $password
     * @return bool
     */
    abstract public function check(string $password): bool;

    /**
     * @return array
     */
    public static function constraints(): array
    {
        return [
            new PasswordLength(),
            new PasswordUpperCase(),
            new PasswordLowerCase(),
            new PasswordNumbers(),
            new PasswordBlackList()
        ];
    }
}
