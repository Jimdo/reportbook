<?php

namespace Jimdo\Reports\Notification;

use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;

class BrowserNotificationService
{
    /** @var string */
    private $repository;

    /**
     * @param BrowserNotificationRepository $repository
     */
    public function __construct(BrowserNotificationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $title
     * @param string $description
     * @param string $userId
     * @param string $reportId
     * @return BrowserNotification
     */
    public function create(string $title, string $description, string $userId, string $reportId): BrowserNotification
    {
        return $this->repository->create($title, $description, $userId, $reportId);
    }

    /**
     * @param BrowserNotification $notification
     */
    public function delete(BrowserNotification $notification)
    {
        $this->repository->delete($notification);
    }

    /**
     * @param string $userId
     * @return array
     */
     public function findByUserId(string $userId): array
     {
         return $this->repository->findByUserId($userId);
        }

    /**
     * @param string $status
     * @return array
     */
    public function findByStatus(string $status): array
    {
        return $this->repository->findByStatus($status);
    }

    /**
     * @param string $id
     * @return BrowserNotification | null
     */
    public function findById(string $id)
    {
        return $this->repository->findById($id);
    }

    /**
     * @param string $id
     */
    public function seen(string $id)
    {
        $notification = $this->repository->findById($id);
        $notification->seen();
        $this->repository->save($notification);
    }
}
