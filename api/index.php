<?php

require_once __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/Serializer.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Jimdo\Reports\Application\ApplicationService;
use Jimdo\Reports\Notification\NotificationService;
use Jimdo\Reports\Notification\BrowserNotificationSubscriber;
use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\Reportbook\TraineeId;

$app = new Silex\Application();
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => 'php://stderr'
));

session_start(['cookie_httponly' => true]);

$serializer = new Serializer();
$appConfig = new ApplicationConfig('../config.yml');

$notificationService = new NotificationService();
$notificationService->register(new BrowserNotificationSubscriber([
    'reportCreated',
    'approvalRequested',
    'reportApproved',
    'reportDisapproved'
], $appConfig));

$appService = ApplicationService::create($appConfig, $notificationService);

$app->before(function (Request $request, Silex\Application $app) {
    if ($request->getMethod() === 'OPTIONS') {
        return new Response(json_encode(null, 204));
    }

    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app->after(function (Request $request, Response $response) {
    $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:3000');
    $response->headers->set('Access-Control-Allow-Credentials', 'true');
    $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Origin');
    $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, DELETE, PUT');
});

$app->options("{anything}", function () {
    return new Response(json_encode(null, 204));
})->assert("anything", ".*");

$app->post('/login', function (Silex\Application $app, Request $request) use ($appService) {
    $username = $request->request->get('username');
    $password = $request->request->get('password');

    if ($appService->authUser($username, $password)) {
        $user = $appService->findUserByUsername($username);
        $_SESSION['userId'] = $user->id();

        return new Response(json_encode(['status' => 'ok']), 200);
    } else {
        $_SESSION['userId'] = '';
        return new Response(json_encode(['status' => 'unauthorized']), 401);
    }
});

$app->get('/reports/{id}', function (Silex\Application $app, $id) use ($appService, $serializer) {
    $report = $appService->findReportById($id, $_SESSION['userId']);

    if ($report !== null) {
        return new Response($serializer->serializeReport($report), 200);
    }
    return new Response(json_encode(['status' => 'unauthorized']), 401);
});

$app->post('/reports', function (Silex\Application $app, Request $request) use ($appService) {
    $report = $appService->createReport(
        new TraineeId($_SESSION['userId']),
        $request->request->get('content'),
        $request->request->get('calendarWeek'),
        $request->request->get('calendarYear'),
        $request->request->get('category')
    );

    if ($report !== null) {
        return new Response(json_encode(['status' => 'created']), 201);
    }
    return new Response(json_encode(['status' => 'unauthorized']), 401);
});

$app->put('/reports/{id}', function (Silex\Application $app, Request $request, $id) use ($appService, $serializer) {
    $report = $appService->editReport(
        $id,
        $request->request->get('content'),
        $request->request->get('calendarWeek'),
        $request->request->get('calendarYear'),
        $request->request->get('category'),
        $request->request->get('status')
    );

    $reports = $appService->findReportsByTraineeId($request->request->get('traineeId'));
    return new Response($serializer->serializeReports($reports), 200);
});

$app->get('/reports', function (Silex\Application $app) use ($appService, $serializer) {
    $reports = $appService->findReportsByTraineeId($_SESSION['userId']);

    return new Response($serializer->serializeReports($reports), 200);
});

$app->delete('/reports/{id}', function (Silex\Application $app, $id) use ($appService, $serializer) {
    $report = $appService->findReportById($id, $_SESSION['userId']);

    if ($report !== null) {
        $appService->deleteReport($id);
        $reports = $appService->findReportsByTraineeId($_SESSION['userId']);

        return new Response($serializer->serializeReports($reports), 200);
    }
    return new Response(json_encode(['status' => 'unauthorized']), 401);
});

$app->get('/profiles', function (Silex\Application $app) use ($appService, $serializer) {
    $user = $appService->findUserById($_SESSION['userId']);
    $profile = $appService->findProfileByUserId($_SESSION['userId']);

    if ($profile !== null) {
        return new Response($serializer->serializeProfile($profile, $user), 200);
    }
    return new Response(json_encode(['status' => 'unauthorized']), 401);
});

$app->put('/profiles', function (Silex\Application $app, Request $request) use ($appService, $serializer) {

    $data = $request->request->all();

    foreach ($data as $key => $value) {
        switch ($key) {
            case 'forename':
                $appService->editForename($_SESSION['userId'], $value);
                break;
                case 'surname':
                $appService->editSurname($_SESSION['userId'], $value);
                break;
            case 'username':
                $appService->editUsername($_SESSION['userId'], $value);
                break;
            case 'email':
                $appService->editEmail($_SESSION['userId'], $value);
                break;
            case 'dateOfBirth':
                $formattedDate = date("Y-m-d", strtotime($value));
                $appService->editDateOfBirth($_SESSION['userId'], $formattedDate);
                break;
            case 'company':
                $appService->editCompany($_SESSION['userId'], $value);
                break;
            case 'jobTitle':
                $appService->editJobTitle($_SESSION['userId'], $value);
                break;
            case 'school':
                $appService->editSchool($_SESSION['userId'], $value);
                break;
            case 'grade':
                $appService->editGrade($_SESSION['userId'], $value);
                break;
            case 'trainingYear':
                $appService->editTrainingYear($_SESSION['userId'], $value);
                break;
            case 'startOfTraining':
                $formattedDate = date("Y-m-d", strtotime($value));
                $appService->editStartOfTraining($_SESSION['userId'], $formattedDate);
                break;
        }
    }

    $user = $appService->findUserById($_SESSION['userId']);
    $profile = $appService->findProfileByUserId($_SESSION['userId']);

    return new Response($serializer->serializeProfile($profile, $user), 200);
});

$app->put('/users', function (Silex\Application $app, Request $request) use ($appService, $serializer) {
    $currentPassword = $request->request->get('currentPassword');
    $newPassword = $request->request->get('newPassword');
    $passwordConfirmation = $request->request->get('passwordConfirmation');

    if ($newPassword === $passwordConfirmation) {
        $appService->editPassword($_SESSION['userId'], $currentPassword, $newPassword);
    }

    return new Response(json_encode(['status' => 'ok']), 200);
});

$app->run();
