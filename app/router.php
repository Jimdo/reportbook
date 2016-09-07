<?php

namespace Jimdo\Reports\Web;

require 'bootstrap.php';

$router = new Router();

$uri = $_SERVER['REQUEST_URI'];

$stderr = fopen('php://stderr', 'w');
$stdout = fopen('php://stdout', 'w');

try {
    $router->dispatch($uri);
    info("Dispatched '$uri'");
} catch (ControllerNotFoundException $e) {
    err("No controller found for '$uri'");
}

function err(string $msg)
{
    global $stderr;
    fwrite($stderr, "[ERROR] $msg\n");
}

function info(string $msg)
{
    global $stdout;
    fwrite($stdout, "[INFO] $msg\n");
}

fclose($stdout);
fclose($stderr);
