<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Comment\CommentService as CommentService;
use Jimdo\Reports\Comment\CommentMongoRepository as CommentMongoRepository;
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
        $date = date('d.m.Y H:m:i');
        $reportId = $this->formData('reportId');
        $userId = $this->sessionData('userId');
        $content = $this->formData('content');
        $traineeId = $this->formData('traineeId');

        $this->commentService->createComment(
            $reportId,
            $this->sessionData('userId'),
            $date,
            $this->formData('content')
        );

        $comment = $this->commentService->findCommentsByReportId($reportId);

        $this->redirect("/report/viewReport?reportId=$reportId&traineeId=$traineeId");
    }
}
