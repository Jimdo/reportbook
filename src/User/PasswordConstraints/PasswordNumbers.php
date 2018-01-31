<?php

namespace Jimdo\Reports\User\PasswordConstraints;

class PasswordNumbers extends PasswordConstraintsFactory
{
    /**
     * @param string $password
     * @return bool
     */
    public function check(string $password): bool
    {
        $numbers = 0;
        for ($i=0; $i < strlen($password); $i++) {
            if (ctype_digit($password[$i])) {
                $numbers += 1;
            }
        }

        if ($numbers >= 2) {
            return true;
        }
        return false;
    }
}
