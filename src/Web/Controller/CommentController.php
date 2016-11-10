<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Reportbook\CommentService as CommentService;
use Jimdo\Reports\Reportbook\ReportbookService as ReportbookService;
use Jimdo\Reports\Reportbook\CommentMongoRepository as CommentMongoRepository;
use Jimdo\Reports\Reportbook\ReportMongoRepository as ReportMongoRepository;
use Jimdo\Reports\Web\Request as Request;
use Jimdo\Reports\Web\Response as Response;
use Jimdo\Reports\Web\RequestValidator as RequestValidator;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;
use Jimdo\Reports\Notification\NotificationService;
use Jimdo\Reports\Notification\PapertrailSubscriber;

class CommentController extends Controller
{
    /** @var ReportbookService */
    private $service;

    /**
     * @param Request $request
     * @param RequestValidator $requestValidator
     * @param ApplicationConfig $appConfig
     * @param Response $response
     */
    public function __construct(
        Request $request,
        RequestValidator $requestValidator,
        ApplicationConfig $appConfig,
        Response $response
    ) {
        parent::__construct($request, $requestValidator, $appConfig, $response);

        $uri = sprintf('mongodb://%s:%s@%s:%d/%s'
            , $this->appConfig->mongoUsername
            , $this->appConfig->mongoPassword
            , $this->appConfig->mongoHost
            , $this->appConfig->mongoPort
            , $this->appConfig->mongoDatabase
        );

        $client = new \MongoDB\Client($uri);

        $eventTypes = [
            'commentCreated',
            'commentDeleted',
            'commentEdited'
        ];

        $notificationService = new NotificationService();

        $reportRepository = new ReportMongoRepository($client, new Serializer(), $appConfig);
        $commentRepository = new CommentMongoRepository($client, new Serializer(), $appConfig);
        $this->service = new ReportbookService($reportRepository, new CommentService($commentRepository), $appConfig, $notificationService);

        $notificationService->register(new PapertrailSubscriber($eventTypes, $appConfig));
    }

    public function createCommentAction()
    {
        $date = date('d.m.Y H:i:s');
        $reportId = $this->formData('reportId');
        $content = $this->formData('content');
        $traineeId = $this->formData('traineeId');
        $userId = $this->sessionData('userId');

        $this->service->createComment(
            $reportId,
            $userId,
            $date,
            $content
        );

        $comments = $this->service->findCommentsByReportId($reportId);

        $queryParams = [
            'reportId' => $reportId,
            'traineeId' => $traineeId
        ];
        $this->redirect("/report/viewReport", $queryParams);
    }

    public function editCommentAction()
    {
        $comment = $this->service->findCommentById($this->formData('commentId'));

        $commentId = $comment->id();
        $newContent = $this->formData('newComment');
        $userId = $this->formData('userId');
        $reportId = $this->formData('reportId');
        $traineeId = $this->formData('traineeId');

        $noPermissions = false;

        try {
            $this->service->editComment($commentId, $newContent, $userId);
        } catch (\Jimdo\Reports\Reportbook\ReportbookServiceException $e) {
            $errorMessages = $this->getErrorMessageForErrorCode($e->getCode());
        }

        if ($errorMessages === null) {
            $queryParams = [
                'reportId' => $reportId,
                'traineeId' => $traineeId
            ];
            $this->redirect("/report/viewReport", $queryParams);
        } else {
            header("Content-type: text/html");
            echo "<h1>$errorMessages</h1>";
            http_response_code(401);
        }
    }

    public function deleteCommentAction()
    {
        $commentId = $this->formData('commentId');
        $reportId = $this->formData('reportId');
        $traineeId = $this->formData('traineeId');
        $userId = $this->formData('userId');

        try {
            $this->service->deleteComment($commentId, $userId);
        } catch (\Jimdo\Reports\Reportbook\ReportbookServiceException $e) {
            $errorMessages = $this->getErrorMessageForErrorCode($e->getCode());
        }

        if ($errorMessages === null) {
            $queryParams = [
                'reportId' => $reportId,
                'traineeId' => $traineeId
            ];
            $this->redirect("/report/viewReport", $queryParams);
        } else {
            header("Content-type: text/html");
            echo "<h1>$errorMessages</h1>";
            http_response_code(401);
        }
    }

    /**
     * @param int $errorCode
     */
    public function getErrorMessageForErrorCode(int $errorCode)
    {
        switch ($errorCode) {
            case ReportbookService::ERR_EDIT_COMMENT_DENIED:
                return 'Du darfst diesen Kommentar nicht bearbeiten!' . "\n";

            case ReportbookService::ERR_DELETE_COMMENT_DENIED:
                return 'Du darfst diesen Kommentar nicht löschen!' . "\n";
        }
    }
}
