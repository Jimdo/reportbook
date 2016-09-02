<?php

namespace Jimdo\Reports;

use Jimdo\Reports\User as User;

class UserRepository
{
    public function createUser(string $forename, string $surname, string $email, string $role)
    {
        return new User($forename, $surname, $email, $role);
    }

    
}
