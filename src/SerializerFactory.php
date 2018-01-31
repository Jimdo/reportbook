<?php

namespace Jimdo\Reports;

use Jimdo\Reports\MongoSerializer;
use Jimdo\Reports\MySQLSerializer;
use Jimdo\Reports\Web\ApplicationConfig;

class SerializerFactory
{
    /** @var string */
    private $storage;

    /**
     * @param ApplicationConfig $appConfig
     */
    public function __construct(ApplicationConfig $appConfig)
    {
        $this->storage = $appConfig->storage;
    }

    /**
     * @return mixed
     */
    public function createSerializer()
    {
        if ($this->storage === 'mongo') {
            return new MongoSerializer();
        } elseif ($this->storage === 'mysql') {
            return new MySQLSerializer();
        }
    }
}
