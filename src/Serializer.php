<?php

namespace Jimdo\Reports;

use Jimdo\Reports\User\User as User;
use Jimdo\Reports\User\Role as Role;
use Jimdo\Reports\Reportbook\Comment as Comment;
use Jimdo\Reports\User\UserId as UserId;
use Jimdo\Reports\Profile\Profile as Profile;
use Jimdo\Reports\Reportbook\Report as Report;
use Jimdo\Reports\Reportbook\TraineeId as TraineeId;
use Jimdo\Reports\Reportbook\Category as Category;
use Jimdo\Reports\Notification\BrowserNotification as Notification;

interface Serializer
{
    /**
     * @param User $user
     * @return array
     */
    public function serializeUser(User $user) : array;

    /**
     * @param array $serializedUser
     * @return User
     */
    public function unserializeUser(array $serializedUser) : User;

    /**
     * @param User $user
     * @return array
     */
    public function serializeProfile(Profile $profile) : array;

    /**
     * @param array $serializedProfile
     * @return Profile
     */
    public function unserializeProfile(array $serializedProfile) : Profile;

    /**
     * @param Report $report
     * @return array
     */
    public function serializeReport($report) : array;

    /**
     * @param array $serializedReport
     * @return Report
     */
    public function unserializeReport(array $serializedReport) : Report;

    /**
     * @param Comment $comment
     * @return array
     */
    public function serializeComment(Comment $comment) : array;

    /**
     * @param array $serializedComment
     * @return Comment
     */
    public function unserializeComment(array $serializedComment) : Comment;

    /**
     * @param User $user
     * @return string
     */
    public function serializeWebUser(User $user) : string;

    /**
     * @param Notification $notification
     * @return array
     */
    public function serializeNotification(Notification $notification) : array;

    /**
     * @param array $serializedNotification
     * @return Notification
     */
    public function unserializeNotification(array $serializedNotification) : Notification;
}
