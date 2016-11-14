<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;

$appConfig = new ApplicationConfig(__DIR__ . '/../config.yml');

date_default_timezone_set($appConfig->timezone);
