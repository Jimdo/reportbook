<?php

namespace Jimdo\Reports\Notification;

use Jimdo\Reports\Notification\BrowserNotification;

interface BrowserNotificationRepository
{
    /**
     * @param string $title
     * @param string $description
     * @param string $userId
     * @param string $reportId
     * @return BrowserNotification
     */
    public function create(string $title, string $description, string $userId, string $reportId): BrowserNotification;

    /**
     * @param BrowserNotification $notification
     */
    public function save(BrowserNotification $notification);

    /**
     * @param BrowserNotification $notification
     */
    public function delete(BrowserNotification $notification);

    /**
     * @param string $id
     * @return BrowserNotification
     */
    public function findById(string $id);

    /**
     * @param string $userId
     * @return array
     */
    public function findByUserId(string $userId): array;

    /**
     * @param string $status
     * @return array
     */
    public function findByStatus(string $status): array;
}