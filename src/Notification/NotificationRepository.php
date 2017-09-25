<?php

namespace Jimdo\Reports\Notification;


interface NotificationRepository
{
    /**
     * @param string $title
     * @param string $description
     * @param string $userId
     * @param string $reportId
     * @return Notification
     */
    public function create(string $title, string $description, string $userId, string $reportId): Notification;

    /**
     * @param Notification $notification
     */
    public function save(Notification $notification);

    /**
     * @param Notification $notification
     */
    public function delete(Notification $notification);

    /**
     * @param string $id
     * @return Notification
     */
    public function findById(string $id): Notification;

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