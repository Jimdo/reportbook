<?php

namespace Jimdo\Reports\User;

use PHPUnit\Framework\TestCase;

use Jimdo\Reports\Views\User as ReadOnlyUser;
use Jimdo\Reports\functional\User\UserInMemoryRepository;

class UserServiceTest extends TestCase
{
    /** @var UserService */
    private $userService;

    /** @var UserRepository */
    private $userRepository;

    protected function setUp()
    {
        $this->userRepository = new UserInMemoryRepository();
        $this->userService = new UserService($this->userRepository);
    }

    /**
     * @test
     */
    public function itShouldRegisterTrainee()
    {
        $username = 'Hase';
        $email = 'max.mustermann@hotmail.de';
        $password = 'SecurePassword123';

        $user = $this->userService->registerTrainee($username, $email, $password);

        $this->assertEquals($email, $user->email());

    }

    /**
     * @test
     */
    public function itShouldRegisterTrainer()
    {
        $username = 'Hase';
        $email = 'max.mustermann@hotmail.de';
        $password = 'SecurePassword123';

        $user = $this->userService->registerTrainee($username, $email, $password);

        $this->assertEquals($email, $user->email());

    }

    /**
     * @test
     */
    public function itShouldRegisterAdmin()
    {
        $username = 'Hase';
        $email = 'max.mustermann@hotmail.de';
        $password = 'SecurePassword123';

        $user = $this->userService->registerAdmin($username, $email, $password);

        $this->assertEquals($email, $user->email());

        $this->assertEquals(Role::ADMIN, $user->roleName());
    }

    /**
     * @test
     */
    public function itShouldDeleteUser()
    {
        $username = 'Hase';
        $email = 'max.mustermann@hotmail.de';
        $password = 'SecurePassword123';

        $this->userService->registerTrainee($username, $email, $password);
        $user = $this->userService->findUserByEmail($email);

        $this->assertCount(1, $this->userRepository->findAllUsers());

        $this->userService->deleteUser($user);

        $this->assertCount(0, $this->userRepository->findAllUsers());
    }

    /**
     * @test
     */
    public function itShouldEditPassword()
    {
        $username = 'Hase';
        $email = 'max.mustermann@hotmail.de';
        $oldPassword = 'SecurePassword123';
        $newPassword = 'newPassword123';

        $user = $this->userService->registerTrainer($username, $email, $oldPassword);

        $this->userService->editPassword($user->id(), $oldPassword, $newPassword);

        $user = $this->userService->findUserById($user->id());

        $strategy = PasswordStrategy\PasswordStrategy::for($user);

        $this->assertTrue($strategy->verify($newPassword, $user->password()));
    }

    /**
     * @test
     */
    public function itShouldEditUsername()
    {
        $email = 'max.mustermann@hotmail.de';
        $password = 'SecurePassword123';
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
        $password = 'SecurePassword123';
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
        $password = 'SecurePassword123';

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
        $username = 'Hase';
        $email = 'max.mustermann@hotmail.de';
        $password = 'SecurePassword123';

        $user = $this->userService->registerTrainee($username, $email, $password);

        $this->assertEquals(Role::TRAINEE, $user->roleName());
    }

    /**
     * @test
     */
    public function itShouldApproveRole()
    {
        $username = 'Hase';
        $email = 'max.mustermann@hotmail.de';
        $password = 'SecurePassword123';

        $user = $this->userService->registerTrainee($username, $email, $password);

        $this->userService->approveRole($user->email());

        $this->assertEquals(Role::STATUS_APPROVED, $user->roleStatus());
    }

    /**
     * @test
     */
    public function itShouldDisApproveRole()
    {
        $username = 'Hase';
        $email = 'max.mustermann@hotmail.de';
        $password = 'SecurePassword123';

        $user = $this->userService->registerTrainee($username, $email, $password);

        $this->userService->disapproveRole($user->email());

        $this->assertEquals(Role::STATUS_DISAPPROVED, $user->roleStatus());
    }

    /**
     * @test
     */
    public function itShouldSaveStatusAfterApproveOrDisapproveRole()
    {
        $username = 'Hase';
        $email = 'max.mustermann@hotmail.de';
        $password = 'SecurePassword123';

        $user = $this->userService->registerTrainee($username, $email, $password);

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
        $password = 'SecurePassword12310';

        $expectedUser1 = $this->userService->registerTrainee('Hase', $email, $password);
        $users = $this->userService->findUsersByStatus(Role::STATUS_NOT_APPROVED);

        $this->assertCount(1, $users);

        $expectedUser2 = $this->userService->registerTrainee('Igel', 'maxi.mustermann@hotmail.de', $password);
        $users = $this->userService->findUsersByStatus(Role::STATUS_NOT_APPROVED);
        $this->assertCount(2, $users);

        $expectedUser3 = $this->userService->registerTrainee('Hans', 'peter.mustermann@web.de', $password);
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
        $password = 'SecurePassword12310';

        $this->assertFalse($this->userService->exists($username));
        $this->assertFalse($this->userService->exists($mail));

        $expectedUser1 = $this->userService->registerTrainee($username, $mail, $password);

        $this->assertTrue($this->userService->exists($username));
        $this->assertTrue($this->userService->exists($mail));
    }

    /**
     * @test
     */
    public function itShouldAuthUserByVerifyingWithCurrentHashedPassword()
    {
        $username = 'max_mustermann';
        $email = 'max.mustermann@hotmail.de';
        $password = 'SecurePassword123';

        $user = $this->userService->registerTrainee($username, $email, $password);

        $correctHashedPassword = 'SecurePassword123';
        $this->assertTrue($this->userService->authUser($user->email(), $correctHashedPassword));
        $this->assertTrue($this->userService->authUser($user->username(), $correctHashedPassword));

        $wrongHashedPassword = 'some wrong Password123';
        $this->assertFalse($this->userService->authUser($user->username(), $wrongHashedPassword));
        $this->assertFalse($this->userService->authUser($user->email(), $wrongHashedPassword));
    }

    /**
     * @test
     * @expectedException Jimdo\Reports\User\ProfileException
     */
    public function itShouldThrowExceptionIfUsernameStringIsEmpty()
    {
        $username = 'max_mustermann';
        $email = 'max.mustermann@hotmail.de';
        $password = 'SecurePassword123';

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
        $password = 'SecurePassword123';

        $user = $this->userService->registerTrainee($username, $email, $password);

        $invalidEmail = '';
        $this->userService->editEmail($user->id(), $invalidEmail);
    }

    /**
     * @test
     * @expectedException Jimdo\Reports\User\PasswordException
     */
    public function itShouldThrowExceptionIfPasswordInsecure()
    {
        $username = 'max_mustermann';
        $email = 'max.mustermann@hotmail.de';
        $password = 'insecurePassword';

        $user = $this->userService->registerTrainee($username, $email, $password);
    }

    /**
     * @test
     */
    public function itShouldCheckIfAdminExists()
    {
        $username = 'max_mustermann';
        $email = 'max.mustermann@hotmail.de';
        $password = 'SecurePassword123';

        $this->assertFalse($this->userService->checkForAdmin());

        $user = $this->userService->registerAdmin($username, $email, $password);

        $this->assertEquals($user->roleStatus(), Role::STATUS_APPROVED);
        $this->assertTrue($this->userService->checkForAdmin());
    }

    /**
     * @test
     */
    public function itShouldFindAllTrainees()
    {
        $user1 = $this->userService->registerTrainer('max_mustermann', 'max.mustermann@hotmail.de', 'SecurePassword123');
        $user2 = $this->userService->registerTrainee('max_mustermann1', 'max1.mustermann@hotmail.de', 'SecurePassword123');
        $user3 = $this->userService->registerTrainee('max_mustermann2', 'max2.mustermann@hotmail.de', 'SecurePassword123');
        $user4 = $this->userService->registerTrainee('max_mustermann3', 'max3.mustermann@hotmail.de', 'SecurePassword123');

        $this->userService->approveRole($user2->email());
        $this->userService->approveRole($user3->email());
        $foundUsers = $this->userService->findAllTrainees();

        $this->assertCount(2, $foundUsers);
        $this->assertEquals($user2->username(), $foundUsers[0]->username());
    }

    /**
     * @test
     */
    public function itShouldFindAllTrainer()
    {
        $user1 = $this->userService->registerTrainee('max_mustermann', 'max.mustermann@hotmail.de', 'SecurePassword123');
        $user2 = $this->userService->registerTrainer('max_mustermann1', 'max1.mustermann@hotmail.de', 'SecurePassword123');
        $user3 = $this->userService->registerTrainer('max_mustermann2', 'max2.mustermann@hotmail.de', 'SecurePassword123');

        $this->userService->approveRole($user1->email());
        $this->userService->approveRole($user2->email());
        $this->userService->approveRole($user3->email());
        $foundUsers = $this->userService->findAllTrainers();

        $this->assertCount(2, $foundUsers);
        $this->assertEquals($user2->username(), $foundUsers[0]->username());
    }
}
