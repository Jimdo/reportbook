<?php

namespace Jimdo\Reports\Application;

use PHPUnit\Framework\TestCase;

use Jimdo\Reports\Reportbook\ReportMongoRepository;
use Jimdo\Reports\Reportbook\CommentMongoRepository;
use Jimdo\Reports\Reportbook\CommentService;
use Jimdo\Reports\Reportbook\ReportbookService;
use Jimdo\Reports\Reportbook\Category;
use Jimdo\Reports\Reportbook\TraineeId;

use Jimdo\Reports\User\UserMongoRepository;
use Jimdo\Reports\User\UserService;
use Jimdo\Reports\User\Role;

use Jimdo\Reports\Profile\ProfileMongoRepository;
use Jimdo\Reports\Profile\ProfileService;

use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\Notification\DummySubscriber;
use Jimdo\Reports\Notification\NotificationService;
use Jimdo\Reports\Serializer;

class ApplicationServiceTest extends TestCase
{
    /** @var ReportbookService */
    private $reportbookService;

    /** @var UserService */
    private $userService;

    /** @var ProfileService */
    private $profileService;

    /** @var ApplicationService */
    private $applicationService;

    protected function setUp()
    {
        $applicationConfig = new ApplicationConfig(__DIR__ . '/../../config.yml');

        $dummySubscriber = new DummySubscriber(['dummyEvent']);
        $notificationService = new NotificationService();
        $notificationService->register($dummySubscriber);

        $uri = sprintf('mongodb://%s:%s@%s:%d/%s'
            , $applicationConfig->mongoUsername
            , $applicationConfig->mongoPassword
            , $applicationConfig->mongoHost
            , $applicationConfig->mongoPort
            , $applicationConfig->mongoDatabase
        );

        $serializer = new Serializer();
        $client = new \MongoDB\Client($uri);

        $reportRepository = new ReportMongoRepository($client, $serializer, $applicationConfig);
        $commentRepository = new CommentMongoRepository($client, $serializer, $applicationConfig);
        $commentService = new CommentService($commentRepository);
        $this->reportbookService = new ReportbookService($reportRepository, $commentService, $applicationConfig, $notificationService);

        $userRepository = new UserMongoRepository($client, $serializer, $applicationConfig);
        $this->userService = new UserService($userRepository, $applicationConfig, $notificationService);


        $profileRepository = new ProfileMongoRepository($client, $serializer, $applicationConfig);
        $this->profileService = new ProfileService($profileRepository, $applicationConfig->defaultProfile, $applicationConfig, $notificationService);

        $this->applicationService = new ApplicationService($this->reportbookService, $this->userService, $this->profileService);
    }

    /**
     * @test
     */
    public function itShouldCompletelyDeleteUser()
    {
        $username = 'Sharkoon';
        $email = 'sharkoon@hotmail.de';
        $password = 'PAssw154asd7asdasdord1234';

        $user = $this->userService->registerTrainee($username, $email, $password);

        $traineeId = new TraineeId($user->id());
        $content = 'some content';
        $calendarWeek = '34';
        $calendarYear = '2017';
        $category = Category::COMPANY;

        $report = $this->reportbookService->createReport($traineeId, $content, $calendarWeek, $calendarYear, $category);

        $forename = 'SharkonnName';
        $surname = 'Sharkonder';

        $profile = $this->profileService->createProfile($user->id(), $forename, $surname);

        $date = date('d.m.Y');
        $content = 'Hallo';

        $comment = $this->reportbookService->createComment($report->id(), $user->id(), $date, $content);

        $this->applicationService->deleteUser($user);

        $this->assertEquals([], $this->reportbookService->findCommentsByUserId($comment->id()));
        $this->assertNull($this->reportbookService->findById($report->id(), $traineeId->id()));
        $this->assertEquals(null, $this->profileService->findProfileByUserId($user->id()));
        $this->assertEquals(null, $this->userService->findUserById($user->id()));
    }
}
