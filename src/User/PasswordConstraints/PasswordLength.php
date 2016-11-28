<?php

namespace Jimdo\Reports\User\PasswordConstraints;

class PasswordLength extends PasswordConstraintsFactory
{
    const PASSWORD_LENGTH = 7;

    /**
     * @param string $password
     * @return bool
     */
    public function check(string $password): bool
    {
        if (strlen($password) < self::PASSWORD_LENGTH) {
            return false;
        } else {
            return true;
        }
    }
}
