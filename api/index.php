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
use Jimdo\Reports\Reportbook\Report;
use Jimdo\Reports\User\Role;

use function GuzzleHttp\json_encode;
use Mailgun\Mailgun;

$app = new Silex\Application();
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => 'php://stderr'
));

session_start(['cookie_httponly' => true]);

$appConfig = new ApplicationConfig('../config.yml');

$notificationService = new NotificationService();
$notificationService->register(new BrowserNotificationSubscriber([
    'reportCreated',
    'approvalRequested',
    'reportApproved',
    'reportDisapproved'
], $appConfig));

function sendMail(string $emailTo, string $emailSubject, string $emailText, string $attachmentPath, ApplicationConfig $appConfig)
{
    $mg = new Mailgun($appConfig->mailgunKey);
    $msg = $mg->MessageBuilder();

    $msg->setFromAddress('Online Berichtsheft <postmaster@berichtsheft.io>');
    $msg->setSubject($emailSubject);
    $msg->setTextBody($emailText);
    $msg->addToRecipient($emailTo);

    $FILES['attachment'] = array();
    $FILES['attachment'][] = $attachmentPath;

    $mg->post("{$appConfig->mailgunDomain}/messages", $msg->getMessage(), $FILES);
}

// Changes output type to file only for reportbook-frontend
putenv('PRINTER_OUTPUT_TYPE=file');

$appService = ApplicationService::create($appConfig, $notificationService);

$serializer = new Serializer($appService);

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
        return new Response(null, 401);
    }
});

$app->post('/logout', function (Silex\Application $app) use ($appService) {
    $_SESSION['userId'] = '';
    return new Response(json_encode(['status' => 'ok']), 200);
});

$app->post('/register', function (Silex\Application $app, Request $request) use ($appService) {
    if ($request->request->get('password') === $request->request->get('passwordConfirmation')) {
        $appService->registerUser(
            $request->request->get('role'),
            $request->request->get('username'),
            $request->request->get('email'),
            $request->request->get('forename'),
            $request->request->get('surname'),
            $request->request->get('password')
        );
        return new Response(json_encode(['status' => 'ok']), 200);
    }
});

$app->get('/reports/{id}', function (Silex\Application $app, $id) use ($appService, $serializer) {
    $userRole = $appService->findUserById($_SESSION['userId'])->roleName();
    if ($userRole === Role::TRAINER || $userRole === Role::ADMIN) {
        $report = $appService->findReportById($id, $_SESSION['userId'], true);
    } else {
        $report = $appService->findReportById($id, $_SESSION['userId']);
    }

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

$app->put('/reports', function (Silex\Application $app, Request $request) use ($appService, $serializer) {
   $column = $request->request->get('column');
   $reports = $appService->findReportsByTraineeId($_SESSION['userId']);
   switch ($column) {
        case 'category':
            $appService->sortArrayDescending($column, $reports);
            break;
        case 'status':
            $appService->sortReportsByStatus(
                [
                    Report::STATUS_DISAPPROVED,
                    Report::STATUS_REVISED,
                    Report::STATUS_NEW,
                    Report::STATUS_EDITED,
                    Report::STATUS_APPROVAL_REQUESTED,
                    Report::STATUS_APPROVED
                ],
                $reports
            );
            break;
    }
    return new Response($serializer->serializeReports($reports), 200);
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

$app->get('/reports', function (Silex\Application $app, Request $request) use ($appService, $serializer) {
    $user = $appService->findUserById($_SESSION['userId']);

    if ($user->roleName() === Role::TRAINEE) {
        $reports = $appService->findReportsByTraineeId($_SESSION['userId']);
    } elseif ($user->roleName() === Role::ADMIN) {
        $reports = $appService->findAllReports();
    } elseif ($user->roleName() === Role::TRAINER) {
        $reports = array_merge(
            $appService->findReportsByStatus(Report::STATUS_APPROVAL_REQUESTED),
            $appService->findReportsByStatus(Report::STATUS_APPROVED),
            $appService->findReportsByStatus(Report::STATUS_DISAPPROVED),
            $appService->findReportsByStatus(Report::STATUS_REVISED)
        );
    }

    if ($request->query->get('search') !== null && $request->query->get('search') !== 'undefined') {
        $reports = $appService->findReportsByString($request->query->get('search'), $user->id(), $user->roleName());
    }

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

$app->get('/reports/users/{id}', function (Silex\Application $app, $id) use ($appService, $serializer) {
    $reports = $appService->findReportsByTraineeId($id);

    if ($reports !== null) {
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

$app->get('/user', function (Silex\Application $app) use ($appService, $serializer) {
    $user = $appService->findUserById($_SESSION['userId']);
    return new Response($serializer->serializeUser($user), 200);
});

$app->put('/user', function (Silex\Application $app, Request $request) use ($appService, $serializer) {
    $appService->editUser($_SESSION['userId'], $request->request->get('username'), $request->request->get('email'));
    return new Response($serializer->serializeUser($appService->findUserById($id)), 200);
});

$app->put('/user/password', function (Silex\Application $app, Request $request) use ($appService) {
    $currentPassword = $request->request->get('currentPassword');
    $newPassword = $request->request->get('newPassword');
    $passwordConfirmation = $request->request->get('passwordConfirmation');

    if ($newPassword === $passwordConfirmation) {
        $appService->editPassword($_SESSION['userId'], $currentPassword, $newPassword);
    }

    return new Response(json_encode(['status' => 'ok']), 200);
});

$app->get('/user/profile', function (Silex\Application $app) use ($appService, $serializer) {
    $profile = $appService->findProfileByUserId($_SESSION['userId']);

    if ($profile !== null) {
        return new Response($serializer->serializeProfile($profile), 200);
    }
    return new Response(json_encode(['status' => 'unauthorized']), 401);
});

$app->put('/user/profile/image', function (Silex\Application $app, Request $request) use ($appService, $serializer) {
    $base64 = $request->request->get('base64');
    $type = $request->request->get('type');

    $appService->editImage($_SESSION['userId'], $base64, $type);

    return new Response(json_encode(['status: ok']), 200);
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

$app->get('/users/trainees', function (Silex\Application $app) use ($appService, $serializer) {
    $trainees = $appService->findAllTrainees();

    if ($trainees !== []) {
        return new Response($serializer->serializeUsers($trainees), 200);
    };

    return new Response(json_encode(['status' => 'unauthorized']), 401);
});

$app->get('/users/{id}/profile', function (Silex\Application $app, $id) use ($appService, $serializer) {
    $profile = $appService->findProfileByUserId($id);

    if ($profile !== null) {
        return new Response($serializer->serializeProfile($profile), 200);
    }
    return new Response(json_encode(['status' => 'unauthorized']), 401);
});

$app->put('/users/{id}/profile', function (Silex\Application $app, Request $request, $id) use ($appService, $serializer) {
    $data = $request->request->all();

    $profile = $appService->editProfile(
        $_SESSION['userId'],
        $data['forename'],
        $data['surname'],
        date("Y-m-d", strtotime($data['dateOfBirth'])),
        $data['school'],
        $data['company'],
        $data['jobTitle'],
        $data['trainingYear'],
        date("Y-m-d", strtotime($data['startOfTraining'])),
        $data['grade']
    );

    return new Response($serializer->serializeProfile($profile), 200);
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

$app->get('/notifications', function (Silex\Application $app) use ($appService, $serializer) {
    $notifications = [];
    foreach ($appService->findNotificationsByUserId($_SESSION['userId']) as $notification) {
        if ($notification->status() != BrowserNotification::STATUS_SEEN) {
            $notifications[] = $notification;
        }
    }
    return new Response($serializer->serializeNotifications($notifications), 200);
});

$app->put('/notifications/{id}', function (Silex\Application $app, Request $request, $id) use ($appService, $serializer) {
    $appService->notificationSeen($id);

    $notifications = [];
    foreach ($appService->findNotificationsByUserId($_SESSION['userId']) as $notification) {
        if ($notification->status() != BrowserNotification::STATUS_SEEN) {
            $notifications[] = $notification;
        }
    }
    return new Response($serializer->serializeNotifications($notifications), 200);
});

$app->get('/download', function (Silex\Application $app) use ($appService, $serializer) {
    $profile = $appService->findProfileByUserId($_SESSION['userId']);

    $startYear = date_parse($profile->startOfTraining())['year'];

    return new Response(json_encode(['year' => $startYear]), 200);
});

$app->post('/download', function (Silex\Application $app, Request $request) use ($appService, $serializer, $appConfig) {
    $category = $request->request->get('category');
    $sex = $request->request->get('sex');
    $forename = $request->request->get('forename');
    $surname = $request->request->get('surname');
    $street = $request->request->get('street');
    $plz = $request->request->get('plz');
    $email = $request->request->get('email');

    switch ($category) {
        case 'reportbook':
            $appService->printReportbook(
                $_SESSION['userId'],
                $sex,
                $forename,
                $surname,
                $street,
                $plz
            );

            sendMail($email, "Dein Berichtsheft", "Im Anhang findest du dein Berichtsheft.", $appConfig->printerOutput . "Berichtsheft.pdf", $appConfig);
            break;
        case 'cover':
            $appService->printCover(
                $_SESSION['userId'],
                $sex,
                $forename,
                $surname,
                $street,
                $plz
            );

            sendMail($email, "Dein Deckblatt", "Im Anhang findest du dein Deckblatt.", $appConfig->printerOutput . "Deckblatt.pdf", $appConfig);
            break;
        case 'report':
            $appService->printReports(
                $_SESSION['userId'],
                $request->request->get('startDateMonth'),
                $request->request->get('startDateYear'),
                $request->request->get('endDateMonth'),
                $request->request->get('endDateYear')
            );

            sendMail($email, "Deine Berichte", "Im Anhang findest du deine Berichte.", $appConfig->printerOutput . "Berichte.pdf", $appConfig);
            break;
    }
    return new Response(json_encode(["status" => "ok"]), 200);
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
