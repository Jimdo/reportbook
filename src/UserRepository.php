<?php

namespace Jimdo\Reports;

use Jimdo\Reports\User as User;

class UserRepository
{
    private $users = [];

    public function createUser(string $forename, string $surname, string $email, string $role)
    {
        $user = new User($forename, $surname, $email, $role);
        $users[] = $user;
        return $user;
    }
}
