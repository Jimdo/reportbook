<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\Request;
use Jimdo\Reports\Web\Response;
use Jimdo\Reports\Web\RequestValidator;
use Jimdo\Reports\Web\ApplicationConfig;

use Jimdo\Reports\Serializer;

use Jimdo\Reports\Notification\NotificationService;
use Jimdo\Reports\Notification\PapertrailSubscriber;
use Jimdo\Reports\Notification\MailgunSubscriber;

use Jimdo\Reports\Application\ApplicationService;

class CommentController extends Controller
{
    /** @var ApplicationService */
    private $appService;

    /**
     * @param Request $request
     * @param RequestValidator $requestValidator
     * @param ApplicationConfig $appConfig
     * @param Response $response
     * @param Twig_Environment $twig
     */
    public function __construct(
        Request $request,
        RequestValidator $requestValidator,
        ApplicationConfig $appConfig,
        Response $response,
        \Twig_Environment $twig
    ) {
        parent::__construct($request, $requestValidator, $appConfig, $response, $twig);

        $eventTypes = [
            'commentCreated',
            'commentDeleted',
            'commentEdited'
        ];

        $notificationService = new NotificationService();
        $this->appService = ApplicationService::create($appConfig, $notificationService);

        $notificationService->register(new PapertrailSubscriber($eventTypes, $appConfig));
        $notificationService->register(new MailgunSubscriber(['commentCreated'], $appConfig));
    }

    public function createCommentAction()
    {
        $date = date('Y-m-d H:i:s');
        $reportId = $this->formData('reportId');
        $content = $this->formData('content');
        $traineeId = $this->formData('traineeId');
        $userId = $this->sessionData('userId');

        $this->appService->createComment(
            $reportId,
            $userId,
            $date,
            $content
        );

        $comments = $this->appService->findCommentsByReportId($reportId);

        $queryParams = [
            'reportId' => $reportId,
            'traineeId' => $traineeId
        ];
        $this->redirect("/report/viewReport", $queryParams);
    }

    public function editCommentAction()
    {
        $comment = $this->appService->findCommentByCommentId($this->formData('commentId'));

        $commentId = $comment->id();
        $newContent = $this->formData('newComment');
        $userId = $this->formData('userId');
        $reportId = $this->formData('reportId');
        $traineeId = $this->formData('traineeId');

        $noPermissions = false;

        try {
            $this->appService->editComment($commentId, $newContent, $userId);
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
            $this->appService->deleteComment($commentId, $userId);
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
