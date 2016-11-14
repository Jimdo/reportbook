<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;

$appConfig = new ApplicationConfig(__DIR__ . '/../config.yml');

if (file_exists(__DIR__ . '/../.env') && $appConfig->mailgunDomain === null) {
    $Loader = new josegonzalez\Dotenv\Loader(__DIR__ . '/../.env');
    $Loader->parse();
    $Loader->toEnv();
}


date_default_timezone_set($appConfig->timezone);
