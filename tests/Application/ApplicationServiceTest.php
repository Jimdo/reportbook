<?php

namespace Jimdo\Reports\Application;

use PHPUnit\Framework\TestCase;

use Jimdo\Reports\Reportbook\Category;
use Jimdo\Reports\Reportbook\TraineeId;
use Jimdo\Reports\User\Role;
use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\Notification\NotificationService;
use Jimdo\Reports\Notification\DummySubscriber;


class ApplicationServiceTest extends TestCase
{
    /** @var ApplicationService */
    private $appService;

    protected function setUp()
    {
        $appConfig = new ApplicationConfig(__DIR__ . '/../../config.yml');

        $dummySubscriber = new DummySubscriber(['dummyEvent']);
        $notificationService = new NotificationService();

        $this->appService = ApplicationService::create($appConfig, $notificationService);
        $notificationService->register($dummySubscriber);
    }

    /**
     * @test
     */
    public function itShouldCompletelyDeleteUser()
    {
        $username = 'Sharkoon';
        $email = 'sharkoon@hotmail.de';
        $password = 'PAssw154asd7asdasdord1234';

        $user = $this->appService->userService->registerTrainee($username, $email, $password);

        $traineeId = new TraineeId($user->id());
        $content = 'some content';
        $calendarWeek = '34';
        $calendarYear = '2017';
        $category = Category::COMPANY;

        $report = $this->appService->reportbookService->createReport($traineeId, $content, $calendarWeek, $calendarYear, $category);

        $forename = 'SharkonnName';
        $surname = 'Sharkonder';

        $profile = $this->appService->profileService->createProfile($user->id(), $forename, $surname);

        $date = date('d.m.Y');
        $content = 'Hallo';

        $comment = $this->appService->reportbookService->createComment($report->id(), $user->id(), $date, $content);

        $user = $this->appService->userService->findUserById($user->id());

        $this->appService->deleteUser($user);

        $this->assertEquals([], $this->appService->reportbookService->findCommentsByUserId($comment->id()));
        $this->assertNull($this->appService->reportbookService->findById($report->id(), $traineeId->id()));
        $this->assertEquals(null, $this->appService->profileService->findProfileByUserId($user->id()));
        $this->assertEquals(null, $this->appService->userService->findUserById($user->id()));
    }
}
