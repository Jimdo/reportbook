<?php

namespace Jimdo\Reports\Web;

use Jimdo\Reports\Report as Report;

class ViewHelper
{
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
            }
        }
}
