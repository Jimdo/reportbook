<?php

namespace Jimdo\Reports\Notification;

use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\MongoSerializer;

class NotificationMongoRepository implements NotificationRepository
{
    /** @var Serializer */
    public $serializer;

    /** @var MongoDB\Client */
    private $client;

    /** @var MongoDB\Database */
    private $reportbook;

    /** @var MongoDB\Collection */
    private $notifications;

    /** @var ApplicationConfig */
    private $applicationConfig;

    /**
     * @param Serializer $serializer
     * @param Client $client
     * @param ApplicationConfig $applicationConfig
     */
    public function __construct(\MongoDB\Client $client, MongoSerializer $serializer, ApplicationConfig $applicationConfig)
    {
        $this->applicationConfig = $applicationConfig;
        $this->serializer = $serializer;
        $this->client = $client;
        $this->reportbook = $this->client->selectDatabase($this->applicationConfig->mongoDatabase);
        $this->notifications = $this->reportbook->notifications;
    }

    /**
     * @param string $title
     * @param string $description
     * @param string $userId
     * @param string $reportId
     * @return Notification
     */
    public function create(string $title, string $description, string $userId, string $reportId): Notification
    {
        $notification = new Notification($title, $description, $userId, $reportId);
        $this->save($notification);

        return $notification;
    }

    /**
     * @param Notification $notification
     */
    public function save(Notification $notification)
    {
        if ($this->findById($notification->id()) === null) {
            $this->notifications->insertOne($this->serializer->serializeNotification($notification));
        } else {
            $this->delete($notification->id());
            $this->notifications->insertOne($this->serializer->serializeNotification($notification));
        }

    }

    /**
     * @param Notification $notification
     */
    public function delete(Notification $notification)
    {
        $this->notifications->deleteOne(['id' => $notification->id()]);
    }

    /**
     * @param string $id
     * @return Notification | null
     */
    public function findById(string $id)
    {
        $serializedNotification = $this->notifications->findOne(['id' => $id]);

        if ($serializedNotification !== null) {
            return $this->serializer->unserializeNotification($serializedNotification->getArrayCopy());
        }
    }

    /**
     * @param string $userId
     * @return array
     */
    public function findByUserId(string $userId): array
    {
        $notifications = [];

        $serializedNotifications = $this->notifications->find();

        foreach ($serializedNotifications as $serializedNotification) {
            if ($serializedNotification['userId'] === $userId) {
                $notifications[] = $this->serializer->unserializeNotification($serializedNotification->getArrayCopy());
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

        $serializedNotifications = $this->notifications->find();

        foreach ($serializedNotifications as $serializedNotification) {
            if ($serializedNotification['status'] === $status) {
                $notifications[] = $this->serializer->unserializeNotification($serializedNotification->getArrayCopy());
            }
        }

        return $notifications;
    }
}
