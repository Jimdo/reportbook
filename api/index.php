<?php

require_once __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/Serializer.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Jimdo\Reports\Application\ApplicationService;
use Jimdo\Reports\Notification\NotificationService;
use Jimdo\Reports\Web\ApplicationConfig;

$app = new Silex\Application();
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => 'php://stderr'
));

$serializer = new Serializer();
$appConfig = new ApplicationConfig('../config.yml');
$notificationService = new NotificationService();
$appService = ApplicationService::create($appConfig, $notificationService);

$app->before(function (Request $request, Silex\Application $app) {
    if ($request->getMethod() === 'OPTIONS') {
        return new Response(json_encode(null, 204));
    }

    if (0 === strpos($request->headers->get('content-type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app->after(function (Request $request, Response $response) {
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Headers', 'X-AUTH-TOKEN, Content-Type, Origin');
    $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, DELETE, PUT');
});

$app->options("{anything}", function () {
    return new Response(json_encode(null, 204));
})->assert("anything", ".*");

$app->run();
