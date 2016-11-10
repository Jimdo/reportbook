<?php

namespace Jimdo\Reports\Web;

use Jimdo\Reports\Reportbook\Report as Report;
use Jimdo\Reports\User\Role as Role;
use Jimdo\Reports\Reportbook\Comment;

class ViewHelper
{
    /**
    * @param string $status
    */
    public function getTranslationForStatus(string $status)
    {
        switch ($status) {
            case Report::STATUS_NEW:
                return 'Neu';
            case Report::STATUS_APPROVED:
                return 'Genehmigt';
            case Report::STATUS_DISAPPROVED:
                return 'Abgelehnt';
            case Report::STATUS_APPROVAL_REQUESTED:
                return 'Eingereicht';
            case Report::STATUS_EDITED:
                return 'Bearbeitet';
            case Report::STATUS_REVISED:
                return 'Überarbeitet';
            case Role::STATUS_NOT_APPROVED:
                return 'Neu';
            case Role::STATUS_APPROVED:
                return 'Freigeschaltet';
            case Role::STATUS_DISAPPROVED:
                return 'Abgelehnt';
            case Comment::STATUS_EDITED;
                return 'Bearbeitet';
        }
    }


    /**
    * @param string $role
    */
    public function getTranslationForRole(string $role)
    {
        switch ($role) {
            case Role::TRAINEE:
                return 'Azubi';
            case Role::TRAINER:
                return 'Ausbilder';
        }
    }
}
