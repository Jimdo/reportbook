<?php

namespace Jimdo\Reports\User;

use Jimdo\Reports\Views\User as ReadOnlyUser;
use Jimdo\Reports\User\Role as Role;
use Jimdo\Reports\ErrorCodeStore;

class UserService
{
    /** @var UserRepository */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $username
     * @param string $email
     * @param string $password
     * @throws UserRepositoryException
     * @return ReadOnlyUser
     */
    public function registerTrainee(
        string $username,
        string $email,
        string $password
    ) {
        $user = $this->registerUser($username, $email, new Role(Role::TRAINEE), $password);
        return $user;
    }

    /**
     * @param string $username
     * @param string $email
     * @param string $password
     * @throws UserRepositoryException
     * @return ReadOnlyUser
     */
    public function registerTrainer(
        string $username,
        string $email,
        string $password
    ) {
        $user = $this->registerUser($username, $email, new Role(Role::TRAINER), $password);
        return $user;
    }

    /**
     * @param string $username
     * @param string $email
     * @param string $password
     * @throws UserRepositoryException
     * @return ReadOnlyUser
     */
    public function registerAdmin(
        string $username,
        string $email,
        string $password
    ) {
        return $this->registerUser($username, $email, new Role(Role::ADMIN), $password);
    }

    /**
     * @param User $user
     */
    public function deleteUser(User $user)
    {
        $this->userRepository->deleteUser($user);
    }

    /**
     * @param string $userId
     * @param string $oldPassword
     * @param string $newPassword
     */
    public function editPassword(string $userId, string $oldPassword, string $newPassword)
    {
        $user = $this->userRepository->findUserById($userId);
        $user->editPassword($oldPassword, $newPassword);

        $this->userRepository->save($user);
    }

    /**
     * @param string $userId
     * @param string $username
     */
    public function editUsername(string $userId, string $username)
    {
        if ($this->exists($username)) {
            throw new ProfileException(
                'The Username already exists!',
                ErrorCodeStore::ERR_USERNAME_EXISTS
            );
        }

        if (empty($username)) {
            throw new ProfileException(
                'It is not allowed to have an empty username!',
                ErrorCodeStore::ERR_USERNAME_EMPTY
            );
        }

        $user = $this->userRepository->findUserById($userId);
        $user->editUsername($username);
        $this->userRepository->save($user, $user->email());
    }

    /**
     * @param string $userId
     * @param string $email
     */
    public function editEmail(string $userId, string $email)
    {
        if ($this->exists($email)) {
            throw new ProfileException(
                'The Email already exists!',
                ErrorCodeStore::ERR_EMAIL_EXISTS
            );
        }

        if (empty($email)) {
            throw new ProfileException(
                'It is not allowed to have an empty email!',
                ErrorCodeStore::ERR_EMAIL_EMPTY
            );
        }

        $user = $this->userRepository->findUserById($userId);
        $user->editEmail($email);
        $this->userRepository->save($user, $user->username());
    }

    /**
     * @param string $userId
     * @param string $forename
     */
    public function editForename(string $userId, string $forename)
    {
        $user = $this->userRepository->findUserById($userId);
        $user->editForename($forename);
        $this->userRepository->save($user);
    }

    /**
     * @param string $userId
     * @param string $surname
     */
    public function editSurname(string $userId, string $surname)
    {
        $user = $this->userRepository->findUserById($userId);
        $user->editSurname($surname);
        $this->userRepository->save($user);
    }

    /**
     * @param string $userId
     * @param string $dateOfBirth
     */
    public function editDateOfBirth(string $userId, string $dateOfBirth)
    {
        $user = $this->userRepository->findUserById($userId);
        $user->editDateOfBirth($dateOfBirth);
        $this->userRepository->save($user);
    }

    /**
     * @param string $userId
     * @param string $school
     */
    public function editSchool(string $userId, string $school)
    {
        $user = $this->userRepository->findUserById($userId);
        $user->editSchool($school);
        $this->userRepository->save($user);
    }

    /**
     * @param string $userId
     * @param string $company
     */
    public function editCompany(string $userId, string $company)
    {
        $user = $this->userRepository->findUserById($userId);
        $user->editCompany($company);
        $this->userRepository->save($user);
    }

    /**
     * @param string $userId
     * @param string $jobTitle
     */
    public function editJobTitle(string $userId, string $jobTitle)
    {
        $user = $this->userRepository->findUserById($userId);
        $user->editJobTitle($jobTitle);
        $this->userRepository->save($user);
    }

    /**
     * @param string $userId
     * @param string $trainingYear
     */
    public function editTrainingYear(string $userId, string $trainingYear)
    {
        $user = $this->userRepository->findUserById($userId);
        $user->editTrainingYear($trainingYear);
        $this->userRepository->save($user);
    }

    /**
     * @param string $userId
     * @param string $startOfTraining
     */
    public function editStartOfTraining(string $userId, string $startOfTraining)
    {
        $user = $this->userRepository->findUserById($userId);
        $user->editStartOfTraining($startOfTraining);
        $this->userRepository->save($user);
    }

    /**
     * @param string $userId
     * @param string $grade
     */
    public function editGrade(string $userId, string $grade)
    {
        $user = $this->userRepository->findUserById($userId);
        $user->editGrade($grade);
        $this->userRepository->save($user);
    }

    /**
     * @param string $userId
     * @param string $image
     */
    public function editImage(string $userId, string $image)
    {
        $user = $this->userRepository->findUserById($userId);
        $user->editImage($image);
        $this->userRepository->save($user);
    }

    /**
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function authUser(string $identifier, string $password): bool
    {
        $user = $this->userRepository->findUserbyEmail($identifier);

        if ($user === null) {
            $user = $this->userRepository->findUserbyUsername($identifier);
        }

        if ($user === null) {
            return false;
        }

        if ($user->verify($password)) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function checkForAdmin(): bool
    {
        foreach ($this->userRepository->findAllUsers() as $user) {
            if ($user->roleName() === Role::ADMIN && $user->roleStatus() === Role::STATUS_APPROVED) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $status
     * @return array
     */
    public function findUsersByStatus(string $status)
    {
        return $users = $this->userRepository->findUsersByStatus($status);
    }

    /**
     * @param string $id
     * @return array
     */
    public function findUserById(string $id)
    {
        return $users = $this->userRepository->findUserById($id);
    }

    /**
     * @param string $username
     * @return array
     */
    public function findUserByUsername(string $username)
    {
        return $users = $this->userRepository->findUserByUsername($username);
    }

    /**
     * @param string $status
     * @return array
     */
    public function findUserByEmail(string $email)
    {
        return $users = $this->userRepository->findUserByEmail($email);
    }

    /**
     * @return array
     */
    public function findAllTrainees(): array
    {
        $users = $this->findUsersByStatus(Role::STATUS_APPROVED);

        $returnUsers = [];
        foreach ($users as $user) {
            if ($user->roleName() === Role::TRAINEE) {
                $returnUsers[] = $user;
            }
        }
        return $returnUsers;
    }

    /**
     * @return array
     */
    public function findAllTrainers(): array
    {
        $users = $this->findUsersByStatus(Role::STATUS_APPROVED);

        foreach ($users as $user) {
            if ($user->roleName() === Role::TRAINER) {
                $returnUsers[] = $user;
            }
        }
        return $returnUsers;
    }

    /**
     * @param string $email
     */
    public function approveRole(string $email)
    {
        $user = $this->userRepository->findUserbyEmail($email);
        $user->approve();
        $this->userRepository->save($user);
    }

    /**
     * @param string $email
     */
    public function disapproveRole(string $email)
    {
        $user = $this->userRepository->findUserbyEmail($email);
        $user->disapprove();
        $this->userRepository->save($user);
    }

    public function ensureUsersPath()
    {
        $this->userRepository->ensureUsersPath();
    }

    /**
     * @param string $identifier
     * @return bool
     */
    public function exists(string $identifier): bool
    {
        return $this->userRepository->exists($identifier);
    }

    /**
     * @param string $username
     * @param string $email
     * @param Role $role
     * @param string $password
     * @throws UserRepositoryException
     * @throws PasswordException
     * @return ReadOnlyUser
     */
    private function registerUser(
        string $username,
        string $email,
        Role $role,
        string $password
    ): ReadOnlyUser {
        $constraints = PasswordConstraints\PasswordConstraintsFactory::constraints();
        foreach ($constraints as $constraint) {
            if (!$constraint->check($password)) {
                throw new PasswordException();
            }
        }

        $hashed = new PasswordStrategy\Hashed();
        $user = $this->userRepository->createUser($username, $email, $role, $hashed->encrypt($password));
        return new ReadOnlyUser($user);
    }
}
