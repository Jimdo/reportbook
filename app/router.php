<?php

namespace Jimdo\Reports\Web;

require 'bootstrap.php';

$router = new Router($appConfig);

$uri = $_SERVER['REQUEST_URI'];

$stderr = fopen('php://stderr', 'w');
$stdout = fopen('php://stdout', 'w');

try {
    $router->dispatch($uri);
    info("Dispatched '$uri'");
} catch (ControllerNotFoundException $e) {
    httpError(400);
    err("No controller found for '$uri'");
} catch (ActionNotFoundException $e) {
    httpError(400);
    err("No action found for '$uri'");
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

function httpError(int $errorCode)
{
    header("Content-type: text/html");
    echo '<h1>Es ist ein Fehler aufgetreten.</h1>';
    http_response_code($errorCode);
}

fclose($stdout);
fclose($stderr);
