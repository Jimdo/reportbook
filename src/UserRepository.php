<?php

namespace Jimdo\Reports;

use Jimdo\Reports\User as User;

class UserRepository
{
    public $users = [];

    public function createUser(string $forename, string $surname, string $email, string $role)
    {
        $user = new User($forename, $surname, $email, $role);
        $this->users[] = $user;
        return $user;
    }

    public function deleteUser(User $deleteUser)
    {
        foreach ($this->users as $key => $user) {
            if ($user === $deleteUser) {
                unset($this->users[$key]);
            }
        }
    }
}
