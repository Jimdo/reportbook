<?php

namespace Jimdo\Reports;

use Jimdo\Reports\User\User;

interface WebSerializer {
    /**
     * @param User $user
     * @return array
     */
    public function serializeWebUser(User $user): array;
}
