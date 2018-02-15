<?php

require_once __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/Serializer.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Jimdo\Reports\Application\ApplicationService;
use Jimdo\Reports\Notification\NotificationService;
use Jimdo\Reports\Notification\BrowserNotificationSubscriber;
use Jimdo\Reports\Notification\BrowserNotification;
use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\Reportbook\TraineeId;
use Jimdo\Reports\User\Role;

use function GuzzleHttp\json_encode;

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

$app->post('/logout', function (Silex\Application $app) use ($appService) {
    $_SESSION['userId'] = '';
    return new Response(json_encode(['status' => 'ok']), 200);
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

$app->get('/reports/{id}/comments', function (Silex\Application $app, $id) use ($serializer, $appService, $addUsersToComments) {
    return new Response($serializer->serializeComments(addUsersToComments($id, $appService)), 200);
});

$app->post('/reports/{id}/comments', function (Silex\Application $app, Request $request, $id) use ($appService, $serializer, $addUsersToComments) {
    $comment = $appService->createComment(
        $id,
        $_SESSION['userId'],
        date('Y-m-d'),
        $request->request->get('content')
    );

    if ($comment !== null) {
        return new Response($serializer->serializeComments(addUsersToComments($id, $appService)), 200);
    } else {
        return new Response(json_encode(['status' => 'unauthorized']), 401);
    }
});

$app->put('/reports/{reportId}/comments/{commentId}', function (
        Silex\Application $app,
        Request $request,
        $reportId,
        $commentId
    ) use ($appService, $serializer, $addUsersToComments) {

    $comment = $appService->editCommentForReport(
        $commentId,
        $_SESSION['userId'],
        $reportId,
        $request->request->get('content')
    );

    if ($comment !== null) {
        return new Response($serializer->serializeComments(addUsersToComments($comment->reportId(), $appService)), 200);
    } else {
        return new Response(json_encode(['status' => 'unauthorized']), 401);
    }
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

    $user = $appService->findUserById($_SESSION['userId']);

    foreach ($data as $key => $value) {
        switch ($key) {
            case 'forename':
                $appService->editForename($_SESSION['userId'], $value);
                break;
                case 'surname':
                $appService->editSurname($_SESSION['userId'], $value);
                break;
            case 'username':
                if ($user->username() !== $value) {
                    $appService->editUsername($_SESSION['userId'], $value);
                }
                break;
            case 'email':
                if ($user->email() !== $value) {
                    $appService->editEmail($_SESSION['userId'], $value);
                }
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

    $profile = $appService->findProfileByUserId($_SESSION['userId']);

    return new Response($serializer->serializeProfile($profile, $user), 200);
});

$app->get('/users/{userId}/profile/image', function (Silex\Application $app, $userId) use ($appService) {
    $user = $appService->findUserById($userId);

    if ($user === null) {
        return new Response(json_encode(['status' => 'unauthorized']), 401);
    }

    $profile = $appService->findProfileByUserId($user->id());
    $base64 = $profile->image();
    $data = base64_decode($base64);

    header('Content-Type: image/' . $profile->imageType());
    header('Pragma: private');
    header('Cache-Control: max-age=86400');

    echo $data;

    return new Response(json_encode(['status' => 'ok']), 200);
});

$app->get('/user/profile/image', function (Silex\Application $app) use ($appService) {
    $user = $appService->findUserById($_SESSION['userId']);

    if ($user === null) {
        return new Response(json_encode(['status' => 'unauthorized']), 401);
    }

    $profile = $appService->findProfileByUserId($user->id());
    $base64 = $profile->image();
    $data = base64_decode($base64);

    header('Content-Type: image/' . $profile->imageType());
    header('Pragma: private');
    header('Cache-Control: max-age=86400');

    echo $data;

    return new Response(json_encode(['status' => 'ok']), 200);
});

$app->get('/user', function (Silex\Application $app) use ($appService, $serializer) {
    $user = $appService->findUserById($_SESSION['userId']);
    return new Response($serializer->serializeUser($user), 200);
});

$app->get('/users', function (Silex\Application $app) use ($appService, $serializer) {
    $user = $appService->findUserById($_SESSION['userId']);

    if ($user->roleName() === Role::TRAINER || $user->roleName() === Role::ADMIN) {
        $users = array_merge(
            $appService->findUsersByStatus(Role::STATUS_APPROVED),
            $appService->findUsersByStatus(Role::STATUS_NOT_APPROVED)
        );
    } else {
        $users = [$user];
    }

    return new Response($serializer->serializeUsers($users), 200);
});

$app->put('/users/{id}/status', function (Silex\Application $app, Request $request, $id) use ($appService, $serializer) {
    $status = $request->request->get('status');

    $user = $appService->findUserById($id);

    if ($user !== null) {
        if ($status === Role::STATUS_APPROVED) {
            $appService->approveUser($user->email());
        }

        if ($status === Role::STATUS_DISAPPROVED) {
            $appService->disapproveUser($user->email());
        }

        $users = array_merge(
            $appService->findUsersByStatus(Role::STATUS_APPROVED),
            $appService->findUsersByStatus(Role::STATUS_NOT_APPROVED)
        );

        return new Response($serializer->serializeUsers($users), 200);
    }

    return new Response(json_encode(['status' => 'unauthorized']), 401);
});

$app->put('/users', function (Silex\Application $app, Request $request) use ($appService) {
    $currentPassword = $request->request->get('currentPassword');
    $newPassword = $request->request->get('newPassword');
    $passwordConfirmation = $request->request->get('passwordConfirmation');

    if ($newPassword === $passwordConfirmation) {
        $appService->editPassword($_SESSION['userId'], $currentPassword, $newPassword);
    }

    return new Response(json_encode(['status' => 'ok']), 200);
});

$app->get('/notifications', function (Silex\Application $app) use ($appService, $serializer) {
    $notifications = [];
    foreach ($appService->findNotificationsByUserId($_SESSION['userId']) as $notification) {
        if ($notification->status() != BrowserNotification::STATUS_SEEN) {
            $notifications[] = $notification;
        }
    }
    return new Response($serializer->serializeNotifications($notifications), 200);
});

$app->put('/notifications', function (Silex\Application $app, Request $request) use ($appService, $serializer) {
    $appService->notificationSeen($request->request->get('id'));

    $notifications = [];
    foreach ($appService->findNotificationsByUserId($_SESSION['userId']) as $notification) {
        if ($notification->status() != BrowserNotification::STATUS_SEEN) {
            $notifications[] = $notification;
        }
    }
    return new Response($serializer->serializeNotifications($notifications), 200);
});

function addUsersToComments(string $reportId, $appService) {
    $comments = $appService->findCommentsByReportId($reportId);

    $commentsWithUsernames = [];

    foreach ($comments as $comment) {
        $user = $appService->findUserById($comment->userId());
        $commentsWithUsernames[] = [
            'comment' => $comment,
            'username' => $user->username()
        ];
    }

    return $commentsWithUsernames;
}

$app->run();
