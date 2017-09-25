<?php

namespace Jimdo\Reports\functional\Notification;

use Jimdo\Reports\Notification\NotificationRepository;
use Jimdo\Reports\Notification\Notification;

class NotificationFakeRepository implements NotificationRepository
{
    private $notifications = [];

    /**
     * @param string $title
     * @param string $description
     * @param string $userId
     * @param string $reportId
     * @return Notification
     */
    public function create(string $title, string $description, string $userId, string $reportId): Notification
    {
        $notification = new Notification($title, $description, $userId,  $reportId);

        $this->save($notification);

        return $notification;
    }

    /**
     * @param Notification $notification
     */
    public function save(Notification $notification)
    {
        $this->notifications[] = $notification;
    }

    /**
     * @param Notification $notification
     */
    public function delete(Notification $notification)
    {
        foreach ($this->notifications as $key => $_notification) {
            if ($_notification->id() === $notification->id()) {
                unset($this->notifications[$key]);
            }
        }
    }

    /**
     * @param string $id
     * @return Notification
     */
    public function findById(string $id): Notification
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
