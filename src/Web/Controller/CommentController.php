<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Reportbook\CommentService as CommentService;
use Jimdo\Reports\Reportbook\CommentMongoRepository as CommentMongoRepository;
use Jimdo\Reports\Web\Request as Request;
use Jimdo\Reports\Web\Response as Response;
use Jimdo\Reports\Web\RequestValidator as RequestValidator;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;

class CommentController extends Controller
{
    /** @var CommentService */
    private $commentService;

    /**
     * @param Request $request
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

        $commentRepository = new CommentMongoRepository($client, new Serializer(), $appConfig);
        $this->commentService = new CommentService($commentRepository);
    }

    public function createCommentAction()
    {
        $date = date('d.m.Y H:i:s');
        $reportId = $this->formData('reportId');
        $content = $this->formData('content');
        $traineeId = $this->formData('traineeId');
        $userId = $this->sessionData('userId');

        $this->commentService->createComment(
            $reportId,
            $userId,
            $date,
            $content
        );

        $comments = $this->commentService->findCommentsByReportId($reportId);

        $this->redirect("/report/viewReport?reportId=$reportId&traineeId=$traineeId");
    }

    public function editCommentAction()
    {
        $comment = $this->commentService->findCommentById($this->queryParams('commentId'));

        $commentId = $comment->id();
        $newContent = $this->formData('newComment');
        $userId = $this->formData('userId');
        $reportId = $this->formData('reportId');
        $traineeId = $this->formData('traineeId');

        $this->commentService->editComment($commentId, $newContent);

        $date = date('d.m.Y H:i:s');

        $this->redirect("/report/viewReport?reportId=$reportId&traineeId=$traineeId");
    }

    public function deleteCommentAction()
    {
        $reportId = $this->formData('reportId');
        $traineeId = $this->formData('traineeId');
        $userId = $this->formData('userId');

        if ($comment->userId() === $userId) {
            $this->commentService->deleteComment($comment->id(), $userId);
        }

        $this->reportbookService->deleteComment($commentId, $userId);

        $this->redirect("/report/viewReport?reportId=$reportId&traineeId=$traineeId");
    }
}
