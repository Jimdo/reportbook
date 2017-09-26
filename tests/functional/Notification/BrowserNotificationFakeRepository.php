<?php

namespace Jimdo\Reports\functional\Notification;

use Jimdo\Reports\Notification\BrowserNotificationRepository;
use Jimdo\Reports\Notification\BrowserNotification;

class BrowserNotificationFakeRepository implements BrowserNotificationRepository
{
    private $notifications = [];

    /**
     * @param string $title
     * @param string $description
     * @param string $userId
     * @param string $reportId
     * @return BrowserNotification
     */
    public function create(string $title, string $description, string $userId, string $reportId): BrowserNotification
    {
        $notification = new BrowserNotification($title, $description, $userId,  $reportId);

        $this->save($notification);

        return $notification;
    }

    /**
     * @param BrowserNotification $notification
     */
    public function save(BrowserNotification $notification)
    {
        $this->notifications[] = $notification;
    }

    /**
     * @param BrowserNotification $notification
     */
    public function delete(BrowserNotification $notification)
    {
        foreach ($this->notifications as $key => $_notification) {
            if ($_notification->id() === $notification->id()) {
                unset($this->notifications[$key]);
            }
        }
    }

    /**
     * @param string $id
     * @return BrowserNotification
     */
    public function findById(string $id): BrowserNotification
    {
        foreach ($this->notifications as $notification) {
            if ($notification->id() === $id) {
                return $notification;
            }
        }
    }

    /**
     * @param string $userId
     * @return array
     */
    public function findByUserId(string $userId): array
    {
        $notifications = [];

        foreach ($this->notifications as $notification) {
            if ($notification->userId() === $userId) {
                $notifications[] = $notification;
            }
        }
        return $notifications;
    }

    /**
     * @param string $status
     * @return array
     */
    public function findByStatus(string $status): array
    {
        $notifications = [];

        foreach ($this->notifications as $notification) {
            if ($notification->status() === $status) {
                $notifications[] = $notification;
            }
        }
        return $notifications;
    }
}
