<?php

namespace Jimdo\Reports\User;

use PHPUnit\Framework\TestCase;

use Jimdo\Reports\User\Role as Role;

class UserFileRepositoryTest extends TestCase
{
    const USERS_ROOT_PATH = 'tests/users';

    protected function setUp()
    {
        $this->deleteRecursive(self::USERS_ROOT_PATH);
    }

    protected function tearDown()
    {
        $this->deleteRecursive(self::USERS_ROOT_PATH);
    }

    /**
     * @test
     * @group ignore
     */
    public function itShouldCreateUser()
    {
        $repository = new UserFileRepository(self::USERS_ROOT_PATH);

        $username = 'Hans';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'SecurePassword123';

        $expectedUser = $repository->createUser($username, $email, $role, $password);

        $userFileName = sprintf('%s/%s', self::USERS_ROOT_PATH, $expectedUser->id());

        $user = unserialize(file_get_contents($userFileName));

        $this->assertEquals($expectedUser->id(), $user->id());
    }

    /**
     * @test
     * @group ignore
     */
    public function itShouldDeleteUser()
    {
        $repository = new UserFileRepository(self::USERS_ROOT_PATH);

        $username = 'Hans';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'SecurePassword123';

        $user = $repository->createUser($username, $email, $role, $password);

        $userFileName = sprintf('%s/%s', self::USERS_ROOT_PATH, $user->id());

        $this->assertTrue(file_exists($userFileName));

        $repository->deleteUser($user);

        $this->assertFalse(file_exists($userFileName));
    }

    /**
     * @test
     * @group ignore
     */
    public function itShouldFindUserByEmail()
    {
        $repository = new UserFileRepository(self::USERS_ROOT_PATH);

        $username = 'Hans';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'SecurePassword123';

        $expectedUser = $repository->createUser($username, $email, $role, $password);

        $user = $repository->findUserByEmail($email);

        $this->assertEquals($expectedUser->id(), $user->id());
    }

    /**
     * @test
     * @group ignore
     */
    public function itShouldFindAllUsers()
    {
        $repository = new UserFileRepository(self::USERS_ROOT_PATH);

        $username = 'Hans';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'SecurePassword123';

        $expectedUser = $repository->createUser($username, $email, $role, $password);

        $foundUsers = $repository->findAllUsers();

        $this->assertCount(1, $foundUsers);

        $expectedUser = $repository->createUser('Igel', 'peter.mustermann@hotmail.de', $role, 'SecurePassword123');
        $foundUsers = $repository->findAllUsers();

        $this->assertCount(2, $foundUsers);
    }

    /**
     * @test
     * @group ignore
     */
    public function itShouldFindUserById()
    {
        $repository = new UserFileRepository(self::USERS_ROOT_PATH);

        $username = 'Hans';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'SecurePassword123';

        $expectedUser = $repository->createUser($username, $email, $role, $password);

        $user = $repository->findUserById($expectedUser->id());

        $this->assertEquals($expectedUser->id(), $user->id());
    }

    /**
     * @test
     * @group ignore
     */
    public function itShouldFindUserByStatus()
    {
        $repository = new UserFileRepository(self::USERS_ROOT_PATH);

        $username = 'Hans';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'SecurePassword123';

        $expectedUser = $repository->createUser($username, $email, $role, $password);
        $users = $repository->findUsersByStatus(Role::STATUS_NOT_APPROVED);

        $this->assertCount(1, $users);

        $expectedUser2 = $repository->createUser('Igel', 'maxi.mustermann@hotmail.de', $role, 'SecurePassword123');
        $users = $repository->findUsersByStatus(Role::STATUS_NOT_APPROVED);

        $this->assertCount(2, $users);
    }

    /**
     * @test
     * @expectedException Jimdo\Reports\User\UserRepositoryException
     * @group ignore
     */
    public function itShouldThrowUserRepositoryExceptionOnDuplicateEmail()
    {
        $userRepository = new UserFileRepository(self::USERS_ROOT_PATH);

        $username = 'Hans';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'SecurePassword123';

        $expectedUser1 = $userRepository->createUser($username, $email, $role, $password);
        $expectedUser2 = $userRepository->createUser('Igel', $email, $role, $password);

    }

    /**
     * @test
     * @group ignore
     */
    public function itShouldCheckIfUserExistsByEmailOrUsername()
    {
        $userRepository = new UserFileRepository(self::USERS_ROOT_PATH);

        $username = 'hase2000';
        $mail = 'hase@123.org';
        $role = new Role('trainer');
        $password = 'SecurePassword123';

        $this->assertFalse($userRepository->exists($username));
        $this->assertFalse($userRepository->exists($mail));

        $userRepository->createUser($username, $mail, $role, $password);

        $this->assertTrue($userRepository->exists($username));
        $this->assertTrue($userRepository->exists($mail));
    }

    /**
     * @test
     * @group ignore
     */
     public function itShouldHaveUniqId()
     {
         $userRepository = new UserFileRepository(self::USERS_ROOT_PATH);

         $forename = 'Max';
         $surname = 'Mustermann';
         $username = 'maxi';
         $email = 'max.mustermann@hotmail.de';
         $role = new Role('trainee');
         $password = 'SecurePassword123';


         $user = $userRepository->createUser($username, $email, $role, $password);

         $this->assertInternalType('string', $user->id());
     }

    private function deleteRecursive($input)
    {
        if (is_file($input)) {
            unlink($input);
            return;
        }

        if (is_dir($input)) {
            foreach (scandir($input) as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $file = join('/', [$input, $file]);
                if (is_file($file)) {
                    unlink($file);
                    continue;
                }

                $this->deleteRecursive($file);

                rmdir($file);
            }
        }
    }
}
