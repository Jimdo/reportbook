<?php

namespace Jimdo\Reports\User\PasswordConstraints;

class PasswordUpperCase extends PasswordConstraintsFactory
{
    /**
     * @param string $password
     * @return bool
     */
    public function check(string $password): bool
    {
        for ($i=0; $i < strlen($password); $i++) {
            if (ctype_upper($password[$i])) {
                return true;
            }
        }
        return false;
    }
}
