<?php

namespace Jimdo\Reports;

use Jimdo\Reports\Reportbook\CommentMongoRepository;
use Jimdo\Reports\Reportbook\CommentMySQLRepository;
use Jimdo\Reports\Reportbook\ReportMongoRepository;
use Jimdo\Reports\Reportbook\ReportMySQLRepository;

use Jimdo\Reports\User\UserMongoRepository;
use Jimdo\Reports\User\UserMySQLRepository;

use Jimdo\Reports\Profile\ProfileMongoRepository;
use Jimdo\Reports\Profile\ProfileMySQLRepository;

use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\Serializer;

class RepositoryFactory
{
    /** @var ApplicationConfig */
    private $appConfig;

    /** @var string */
    private $storage;

    /** @var Serializer */
    private $serializer;

    /** @var MongoDB Client */
    private $mongoClient;

    /** @var PDO */
    private $mysqlClient;

    /**
     * @param ApplicationConfig $appConfig
     */
    public function __construct(ApplicationConfig $appConfig, Serializer $serializer)
    {
        $this->appConfig = $appConfig;
        $this->storage = $this->appConfig->storage;
        $this->serializer = $serializer;

        if ($this->storage === 'mongo') {
            $mongoUri = sprintf('mongodb://%s:%s@%s:%d/%s'
            , $this->appConfig->mongoUsername
            , $this->appConfig->mongoPassword
            , $this->appConfig->mongoHost
            , $this->appConfig->mongoPort
            , $this->appConfig->mongoDatabase
        );
            $this->mongoClient = new \MongoDB\Client($mongoUri);
        } elseif ($this->storage === 'mysql') {
            $mysqlUri = "mysql:host={$this->appConfig->mysqlHost};dbname={$this->appConfig->mysqlDatabase}";
            $this->mysqlClient = new \PDO($mysqlUri, $this->appConfig->mysqlUser, $this->appConfig->mysqlPassword);
        }
    }

    /**
     * @return mixed
     */
    public function createUserRepository()
    {
        if ($this->storage === 'mongo') {
            return new UserMongoRepository($this->mongoClient, $this->serializer, $this->appConfig);
        } elseif ($this->storage === 'mysql') {
            return new UserMySQLRepository($this->mysqlClient, $this->serializer, $this->appConfig);
        }
    }

    /**
     * @return mixed
     */
    public function createReportRepository()
    {
        if ($this->storage === 'mongo') {
            return new ReportMongoRepository($this->mongoClient, $this->serializer, $this->appConfig);
        } elseif ($this->storage === 'mysql') {
            return new ReportMySQLRepository($this->mysqlClient, $this->serializer, $this->appConfig);
        }
    }

    /**
     * @return mixed
     */
    public function createProfileRepository()
    {
        if ($this->storage === 'mongo') {
            return new ProfileMongoRepository($this->mongoClient, $this->serializer, $this->appConfig);
        } elseif ($this->storage === 'mysql') {
            return new ProfileMySQLRepository($this->mysqlClient, $this->serializer, $this->appConfig);
        }
    }

    /**
     * @return mixed
     */
    public function createCommentRepository()
    {
        if ($this->storage === 'mongo') {
            return new CommentMongoRepository($this->mongoClient, $this->serializer, $this->appConfig);
        } elseif ($this->storage === 'mysql') {
            return new CommentMySQLRepository($this->mysqlClient, $this->serializer, $this->appConfig);
        }
    }
}
