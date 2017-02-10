<?php

namespace Jimdo\Reports\Application;

use Jimdo\Reports\Views\User;

use Jimdo\Reports\Reportbook\ReportbookService;
use Jimdo\Reports\User\UserService;
use Jimdo\Reports\Profile\ProfileService;

class ApplicationService
{
    /** @var ReportbookService */
    private $reportbookService;

    /** @var UserService */
    private $userService;

    /** @var ProfileService */
    private $profileService;

    /**
     * @param ReportbookService $reportbookService
     * @param UserService $userService
     * @param ProfileService $profileService
     */
    public function __construct(ReportbookService $reportbookService, UserService $userService, ProfileService $profileService)
    {
        $this->reportbookService = $reportbookService;
        $this->userService = $userService;
        $this->profileService = $profileService;
    }

    /*
     * @param Jimdo\Reports|Views\User $user
     */
    public function deleteUser(User $user)
    {
        $userId = $user->id();

        $comments = $this->reportbookService->findCommentsByUserId($userId);
        foreach ($comments as $comment) {
            $this->reportbookService->deleteComment($comment->id(), $userId);
        }

        $reports = $this->reportbookService->findByTraineeId($userId);
        if ($reports !== []) {
            foreach ($reports as $report) {
                $this->reportbookService->deleteReport($report->id());
            }
        }

        $profile = $this->profileService->findProfileByUserId($userId);
        if ($profile !== null) {
            $this->profileService->deleteProfile($profile);
        }

        $user = $this->userService->findUserById($userId);
        $this->userService->deleteUser($user);
    }
}
