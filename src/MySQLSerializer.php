<?php

namespace Jimdo\Reports;

use Jimdo\Reports\User\User;
use Jimdo\Reports\Reportbook\Comment;
use Jimdo\Reports\Profile\Profile;
use Jimdo\Reports\Reportbook\Report;

interface MySQLSerializer {
    /**
     * @param array $serializedUser
     * @return User
     */
    public function unserializeUser(array $serializedUser): User;

    /**
     * @param array $serializedProfile
     * @return Profile
     */
    public function unserializeProfile(array $serializedProfile): Profile;

    /**
     * @param array $serializedReport
     * @return Report
     */
    public function unserializeReport(array $serializedReport): Report;

    /**
     * @param array $serializedComment
     * @return Comment
     */
    public function unserializeComment(array $serializedComment): Comment;
}
