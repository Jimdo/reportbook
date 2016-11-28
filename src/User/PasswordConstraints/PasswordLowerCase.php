<?php

namespace Jimdo\Reports\User\PasswordConstraints;

class PasswordLowerCase extends PasswordConstraintsFactory
{
    const ERR_CODE = 19;

    /**
     * @param string $password
     * @return bool
     */
    public function check(string $password): bool
    {
        for ($i=0; $i < strlen($password); $i++) {
            if (ctype_lower($password[$i])) {
                return true;
            }
        }
        return false;
    }
}
