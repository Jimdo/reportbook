<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;

if (file_exists('/../.env')) {
    $Loader = new josegonzalez\Dotenv\Loader('/../.env');
    $Loader->parse();
    $Loader->toEnv();
}

$appConfig = new ApplicationConfig(__DIR__ . '/../config.yml');

date_default_timezone_set($appConfig->timezone);
