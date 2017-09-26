<?php

namespace Jimdo\Reports;

use Jimdo\Reports\User\User;
use Jimdo\Reports\Reportbook\Comment;
use Jimdo\Reports\Profile\Profile;
use Jimdo\Reports\Reportbook\Report;
use Jimdo\Reports\Notification\Notification;

interface MongoSerializer {
    /**
     * @param User $user
     * @return array
     */
    public function serializeUser(User $user): array;

    /**
     * @param array $serializedUser
     * @return User
     */
    public function unserializeUser(array $serializedUser): User;

    /**
     * @param User $user
     * @return array
     */
    public function serializeProfile(Profile $profile): array;

    /**
     * @param array $serializedProfile
     * @return Profile
     */
    public function unserializeProfile(array $serializedProfile): Profile;

    /**
     * @param Report $report
     * @return array
     */
    public function serializeReport($report): array;

    /**
     * @param array $serializedReport
     * @return Report
     */
    public function unserializeReport(array $serializedReport): Report;

    /**
     * @param Comment $comment
     * @return array
     */
    public function serializeComment(Comment $comment): array;

    /**
     * @param array $serializedComment
     * @return Comment
     */
    public function unserializeComment(array $serializedComment): Comment;

    /**
    * @param Notification $notification
    * @return array
     */
    public function serializeNotification(Notification $notification): array;

    /**
     * @param array $serializedNotification
     * @return Notification
     */
    public function unserializeNotification(array $serializedNotification): Notification;
}
