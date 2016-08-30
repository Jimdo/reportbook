<?php

namespace Jimdo\Reports\Web;

require 'bootstrap.php';

$router = new Router();
$router->dispatch($_SERVER['REQUEST_URI']);
