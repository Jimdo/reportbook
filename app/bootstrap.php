<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Jimdo\Reports\Web\ApplicationConfig;
use Monolog\Logger;
use Altmetric\MongoSessionHandler;

$applicationConfig = new ApplicationConfig(realpath(__DIR__ . '/../config.yml'));

$uri = sprintf('mongodb://%s:%s@%s:%d/%s'
    , $applicationConfig->mongoUsername
    , $applicationConfig->mongoPassword
    , $applicationConfig->mongoHost
    , $applicationConfig->mongoPort
    , $applicationConfig->mongoDatabase
);

$client = new \MongoDB\Client($uri);
$db = $client->selectDatabase($applicationConfig->mongoDatabase);

$handler = new MongoSessionHandler($db->sessions, new Logger('session'));

session_set_save_handler($handler);

session_start();

date_default_timezone_set("UTC");
