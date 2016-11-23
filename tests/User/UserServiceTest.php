<?php

namespace Jimdo\Reports\User;

use PHPUnit\Framework\TestCase;

use Jimdo\Reports\Views\User as ReadOnlyUser;
use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\Notification\DummySubscriber;
use Jimdo\Reports\Notification\NotificationService;

class UserServiceTest extends TestCase
{
    /** @var UserService */
    private $userService;

    /** @var UserRepository */
    private $userRepository;

    protected function setUp()
    {
        $dummySubscriber = new DummySubscriber(['dummyEvent']);

        $notificationService = new NotificationService();
        $this->userRepository = new UserInMemoryRepository();
        $this->userService = new UserService($this->userRepository, new ApplicationConfig(__DIR__ . '/../../config.yml'), $notificationService);

        $notificationService->register($dummySubscriber);
    }

    /**
     * @test
     */
    public function itShouldRegisterTrainee()
    {
        $email = 'max.mustermann@hotmail.de';
        $password = '123456789';

        $user = $this->userService->registerTrainee('Hase', $email, $password);

        $this->assertEquals($email, $user->email());
    }

    /**
     * @test
     */
    public function itShouldRegisterTrainer()
    {
        $email = 'max.mustermann@hotmail.de';
        $password = '123456789';

        $user = $this->userService->registerTrainer('Hase', $email, $password);

        $this->assertEquals($email, $user->email());
    }

    /**
     * @test
     */
    public function itShouldEditPassword()
    {
        $email = 'max.mustermann@hotmail.de';
        $oldPassword = '123456789';
        $newPassword = '1111111111';

        $user = $this->userService->registerTrainer('Hase', $email, $oldPassword);

        $this->userService->editPassword($user->id(), $oldPassword, $newPassword);

        $user = $this->userService->findUserById($user->id());

        $this->assertEquals($newPassword, $user->password());
    }

    /**
     * @test
     */
    public function itShouldEditUsername()
    {
        $email = 'max.mustermann@hotmail.de';
        $password = '123456789';
        $username = 'jenny';
        $newUsername = 'jennyPenny';

        $user = $this->userService->registerTrainer($username, $email, $password);

        $this->userService->editUsername($user->id(), $newUsername);

        $user = $this->userService->findUserById($user->id());

        $this->assertEquals($newUsername, $user->username());
    }

    /**
     * @test
     */
    public function itShouldEditEmail()
    {
        $email = 'max.mustermann@hotmail.de';
        $newEmail = 'jennyPenny@hotmail.de';
        $password = '123456789';
        $username = 'jenny';

        $user = $this->userService->registerTrainer($username, $email, $password);

        $this->userService->editEmail($user->id(), $newEmail);

        $user = $this->userService->findUserById($user->id());

        $this->assertEquals($newEmail, $user->email());
    }

    /**
     * @test
     */
    public function itShouldAuthUser()
    {
        $username = 'Hase';
        $email = 'max.mustermann@hotmail.de';
        $password = '123456789';

        $user = $this->userService->registerTrainee($username, $email, $password);

        $authStatus = $this->userService->authUser($email, $password);
        $this->assertTrue($authStatus);

        $authStatus = $this->userService->authUser($username, $password);
        $this->assertTrue($authStatus);
    }

    /**
     * @test
     */
    public function itShouldHaveRoleName()
    {
        $email = 'max.mustermann@hotmail.de';
        $password = '123456789';

        $user = $this->userService->registerTrainee('Hase', $email, $password);

        $this->assertEquals(Role::TRAINEE, $user->roleName());
    }

    /**
     * @test
     */
    public function itShouldApproveRole()
    {
        $email = 'max.mustermann@hotmail.de';
        $password = '123456789';

        $user = $this->userService->registerTrainee('Hase', $email, $password);

        $this->userService->approveRole($user->email());

        $this->assertEquals(Role::STATUS_APPROVED, $user->roleStatus());
    }

    /**
     * @test
     */
    public function itShouldDisApproveRole()
    {
        $email = 'max.mustermann@hotmail.de';
        $password = '123456789';

        $user = $this->userService->registerTrainee('Hase', $email, $password);

        $this->userService->disapproveRole($user->email());

        $this->assertEquals(Role::STATUS_DISAPPROVED, $user->roleStatus());
    }

    /**
     * @test
     */
    public function itShouldSaveStatusAfterApproveOrDisapproveRole()
    {
        $email = 'max.mustermann@hotmail.de';
        $password = '123456789';

        $user = $this->userService->registerTrainee('Hase', $email, $password);

        $this->assertFalse($this->userRepository->saveMethodCalled);
        $this->userService->disapproveRole($user->email());
        $this->assertTrue($this->userRepository->saveMethodCalled);

        $this->userRepository->saveMethodCalled = false;

        $this->assertFalse($this->userRepository->saveMethodCalled);
        $this->userService->approveRole($user->email());
        $this->assertTrue($this->userRepository->saveMethodCalled);
    }

    /**
     * @test
     */
    public function itShouldFindUsersByStatus()
    {
        $email = 'max.mustermann@hotmail.de';

        $expectedUser1 = $this->userService->registerTrainee('Hase', $email, '12345678910');
        $users = $this->userService->findUsersByStatus(Role::STATUS_NOT_APPROVED);

        $this->assertCount(1, $users);

        $expectedUser2 = $this->userService->registerTrainee('Igel', 'maxi.mustermann@hotmail.de', '12345678910');
        $users = $this->userService->findUsersByStatus(Role::STATUS_NOT_APPROVED);
        $this->assertCount(2, $users);

        $expectedUser3 = $this->userService->registerTrainee('Hans', 'peter.mustermann@web.de', '12345678910');
        $this->userService->approveRole($expectedUser3->email());
        $users = $this->userService->findUsersByStatus(Role::STATUS_APPROVED);
        $this->assertCount(1, $users);
    }

    /**
     * @test
     */
    public function itShouldCheckIfUserExistsByEmailOrUsername()
    {
        $username = 'hase2000';
        $mail = 'hase@123.org';

        $this->assertFalse($this->userService->exists($username));
        $this->assertFalse($this->userService->exists($mail));

        $expectedUser1 = $this->userService->registerTrainee($username, $mail, '12345678910');

        $this->assertTrue($this->userService->exists($username));
        $this->assertTrue($this->userService->exists($mail));
    }

    /**
     * @test
     * @expectedException Jimdo\Reports\User\ProfileException
     */
    public function itShouldThrowExceptionIfUsernameStringIsEmpty()
    {
        $username = 'max_mustermann';
        $email = 'max.mustermann@hotmail.de';
        $password = 'defaultPassword';

        $user = $this->userService->registerTrainee($username, $email, $password);

        $invalidUsername = '';
        $this->userService->editUsername($user->id(), $invalidUsername);
    }

    /**
     * @test
     * @expectedException Jimdo\Reports\User\ProfileException
     */
    public function itShouldThrowExceptionIfEmailStringIsEmpty()
    {
        $username = 'max_mustermann';
        $email = 'max.mustermann@hotmail.de';
        $password = 'defaultPassword';

        $user = $this->userService->registerTrainee($username, $email, $password);

        $invalidEmail = '';
        $this->userService->editEmail($user->id(), $invalidEmail);
    }
}
