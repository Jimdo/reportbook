<?php

namespace Jimdo\Reports\User\PasswordConstraints;

class PasswordBlackList extends PasswordConstraintsFactory
{
    const ERR_CODE = 21;

    private $blacklist = [
        'Password123',
        '123Password',
        'Abcd1234',
        '1234Abcd'
    ];
    /**
     * @param string $password
     * @return bool
     */
    public function check(string $password): bool
    {
        foreach ($this->blacklist as $blacklistedPassword) {
            if ($blacklistedPassword === $password) {
                return false;
            }
        }
        return true;
    }
}
