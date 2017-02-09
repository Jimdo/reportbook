<?php

namespace Jimdo\Reports\Web;

use Jimdo\Reports\Reportbook\Report as Report;
use Jimdo\Reports\User\Role as Role;
use Jimdo\Reports\Reportbook\Comment;
use Jimdo\Reports\Reportbook\Category;

class ViewHelper
{
    /**
     * @param int $month
     * @param int $year
     * @param array $cwInfo
     */
    public function showMonth(int $month, int $year, array $cwInfo)
    {
        $date = mktime(12, 0, 0, $month, 1, $year);
        $daysInMonth = date("t", $date);

        $offset = date("w", $date) - 1;

        if ($offset === -1) {
            $offset = 6;
        }

        $rows = 1;

        echo "<table data-link=\"row\" class=\"table-condensed table-bordered table-striped table table-curved\">\n";
        echo
        "\t<tr>
            <th class=\"text-center\">KW</th>
            <th class=\"text-center\">Mo</th>
            <th class=\"text-center\">Di</th>
            <th class=\"text-center\">Mi</th>
            <th class=\"text-center\">Do</th>
            <th class=\"text-center\">Fr</th>
            <th class=\"text-center\">Sa</th>
            <th class=\"text-center\">So</th>
        </tr>";
        echo "\n\t<tr>";

        for($i = 1; $i <= $offset; $i++)
        {
            if ($offset === 1 && $month === 1) {
                echo "<td style=\"font-weight: bold;\" class=\"text-center\">1</td>";
            } else {
                echo "<td></td>";
            }

        }

        for($day = 1; $day <= $daysInMonth; $day++) {

            $currentWeek = intVal(date("W", strtotime("$year-$month-$day")));

            if( ($day + $offset - 1) % 7 == 0 && $day != 1) {

                $reportStatus = $cwInfo[$currentWeek]['status'];
                $reportTraineeId = $cwInfo[$currentWeek]['traineeId'];
                $reportReportId = $cwInfo[$currentWeek]['reportId'];

                $linkForWeek = ($reportStatus === Report::STATUS_APPROVAL_REQUESTED ||
                    $reportStatus === Report::STATUS_APPROVED ||
                    $reportStatus === Report::STATUS_DISAPPROVED);

                $link = '';
                if ($linkForWeek === true) {
                    $link = "<form action=\"/report/viewReport\" method=\"post\">
                                <a style=\"color:black; font-weight: bold;\"href=\"javascript:;\" onclick=\"parentNode.submit();\">$currentWeek</a>
                                <input type=\"hidden\" name=\"traineeId\" value=\"$reportTraineeId\"/>
                                <input type=\"hidden\" name=\"reportId\" value=\"$reportReportId\"/>
                            </form>";
                } else {
                    $link = $currentWeek;
                }
                echo "</tr>\n\t<tr><td style=\"font-weight: bold;\" class=\"text-center\">$link</td>";

                $rows++;
            } elseif ($day === 1 && $offset == 0) {
                echo "<td style=\"font-weight: bold;\" class=\"text-center\">$link</td>";
            } elseif ($day === 1) {
                echo "<td></td>";
            }

            if ($this->checkIfDayIsInCalendarWeek(
                $day, $currentWeek, $month, $year) &&
                array_key_exists($currentWeek, $cwInfo) &&
                $cwInfo[$currentWeek]['status'] !== ''
            ) {

                switch ($cwInfo[$currentWeek]['status']) {
                    case Report::STATUS_APPROVAL_REQUESTED:
                        $color = 'yellow';
                        break;
                    case Report::STATUS_APPROVED:
                        $color = '#01DF01';
                        break;
                    case Report::STATUS_DISAPPROVED:
                    $color = 'red';
                        break;
                    default:
                    $color = '';
                }
                echo "<td bgcolor=\"$color\" class=\"text-center\">" . $day .  "</td>";
            } else {
                echo "<td class=\"text-center\">" . $day .  "</td>";
            }
        }

        while( ($day + $offset) <= $rows * 7) {
            echo "<td></td>";
            $day++;
        }

        echo "</tr>\n";
        echo "</table>\n";
    }

    /**
    * @param string $status
    * @return string
    */
    public function getTranslationForStatus(string $status): string
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
    * @return string
    */
    public function getTranslationForRole(string $role): string
    {
        switch ($role) {
            case Role::TRAINEE:
                return 'Azubi';
            case Role::TRAINER:
                return 'Ausbilder';
            case Role::ADMIN:
                return 'Administrator';
        }
    }

    /**
    * @param string $category
    * @return string
    */
    public function getTranslationForCategory(string $category): string
    {
        switch ($category) {
            case Category::COMPANY:
                return 'Betrieb';
            case Category::SCHOOL:
                return 'Schule';
        }
    }

    /**
     * @param int $week
     * @param int $year
     * @return array
     */
     public function getStartAndEndDate(int $week, int $year): array
     {
        $date = new \DateTime();

        $date->setISODate($year, $week);

        $startYear = intVal($date->format('Y'));
        $startMonth = intVal($date->format('m'));
        $startDay = intVal($date->format('d'));

        // Adds six days for the end of the week
        $date->modify('+6 days');

        $endYear = intVal($date->format('Y'));
        $endMonth = intVal($date->format('m'));
        $endDay = intVal($date->format('d'));

        return [
            $startYear,
            $startMonth,
            $startDay,
            $endYear,
            $endMonth,
            $endDay
        ];
     }

    /**
     * @param int $day
     * @param int $month
     * @param int $year
     * @return bool
     */
    public function checkIfDayIsInCalendarWeek(int $day, int $week, int $month, int $year)
    {
        $weekInfo = $this->getStartAndEndDate($week, $year);
        $startYearMonth = "$weekInfo[0]-$weekInfo[1]";
        $endYearMonth = "$weekInfo[3]-$weekInfo[4]";
        $startDay = $weekInfo[2];
        $endDay = $weekInfo[5];
        $day = intVal($day);

        if ("$year-$month" == $startYearMonth) {

            if ($day >= $startDay && $day < ($startDay + 7)) {
                    return true;
            }
        }

        if ("$year-$month"  == $endYearMonth && $day <= $endDay){
            return true;
        }
        return false;
    }
}
