<?php

namespace Jimdo\Reports\Printer;

use Jimdo\Reports\Profile\ProfileService;
use Jimdo\Reports\Reportbook\ReportbookService;
use Jimdo\Reports\Web\ApplicationConfig;

class PrintService
{
    /** @var mPDF */
    private $mpdf;

    /** @var ReportbookService */
    private $reportService;

    /** @var ProfileService */
    private $profileService;

    /** @var ApplicationConfig */
    private $appConfig;

    /** @var Twig_Environment */
    private $twig;

    /** @var string */
    private $outputDir;

    /**
     * @param ProfileService
     * @param ReportbookService
     * @param ApplicationConfig
     */
    public function __construct(ProfileService $profileService, ReportbookService $reportService, ApplicationConfig $appConfig)
    {
        $this->appConfig = $appConfig;
        $this->reportService = $reportService;
        $this->profileService = $profileService;
        $this->mpdf = new \mPDF();

        $loader = new \Twig_Loader_Filesystem(__DIR__ . $this->appConfig->printerTemplates);
        $this->twig = new \Twig_Environment($loader);

        $this->outputDir = $this->appConfig->printerOutput;

        if (getenv('APPLICATION_ENV') === 'test') {
            $this->outputDir = realpath(__DIR__ . '/../../' . $this->appConfig->printerOutput);
        }
    }

    /**
     * @param string $userId
     * @param string $trainerTitle
     * @param string $trainerForename
     * @param string $trainerSurname
     * @param string $companyStreet
     * @param string $companyCity
     */
    public function printCover(string $userId, string $trainerTitle, string $trainerForename, string $trainerSurname, string $companyStreet, string $companyCity, bool $printWholeReportbook = false)
    {
      $profile = $this->profileService->findProfileByUserId($userId);

      $template = $this->twig->load('Cover.html');

      $variables = [
          'trainerTitle' => $trainerTitle,
          'traineeForename' => $profile->forename(),
          'traineeSurname' => $profile->surname(),
          'trainerForename' => $trainerForename,
          'trainerSurname' => $trainerSurname,
          'companyName' => $profile->company(),
          'companyStreet' => $companyStreet,
          'companyCity' => $companyCity
      ];

      $this->mpdf->WriteHTML($template->render($variables));

      if ($printWholeReportbook) {
          $this->mpdf->AddPage();
      } else {
          $this->mpdf->Output($this->outputDir . '/Deckblatt.pdf','F');
      }
    }

    /**
    * @param string $userId
    * @param string $startMonth
    * @param string $startYear
    * @param string $endMonth
    * @param string $endYear
    */
    public function printReports(string $userId, string $startMonth, string $startYear, string $endMonth, string $endYear, bool $printWholeReportbook = false)
    {
        $profile = $this->profileService->findProfileByUserId($userId);
        $reports = $this->reportService->findByTraineeId($userId);

        if (!$printWholeReportbook) {
            $reports = $this->getReportsForPeriod($reports, $startMonth, $startYear, $endMonth, $endYear);
        }
        $weekInfo = $this->createArrayForStartAndEndOfWeek($reports);


        $maxLinesPerSite = 18;
        $reportsPerSite = [];
        $reportsPerSite[] = [];
        $site = 0;
        $reportNumber = 0;
        $lines = 0;

        foreach ($reports as $run => $report) {
            $currentLines = $this->countLines($report->content());

            if ($lines + $currentLines <= $maxLinesPerSite) {
                $lines += $currentLines;
                $reportNumber++;
            } else {
                $lines = $currentLines;
                $site++;
                $reportNumber = 0;
            }

            if (!isset($reportsPerSite[$site][$reportNumber])) {
                $reportsPerSite[$site][$reportNumber] = [];
            }

            $reportsPerSite[$site][$reportNumber] = [
                'report' => $report,
                'weekInfo' => ['start' => $weekInfo[$run][0], 'end' => $weekInfo[$run][1]]
            ];
        }

        $template = $this->twig->load('Report.html');

        foreach ($reportsPerSite as $run => $site) {

            $variables = [
                'traineeForename' => $profile->forename(),
                'traineeSurname' => $profile->surname(),
                'jobTitle' => $profile->jobTitle(),
                'site' => $site
            ];


            if (count($reportsPerSite) - 1 !== $run) {
                $this->mpdf->WriteHTML($template->render($variables));
                $this->mpdf->AddPage();
            } else {
                $this->mpdf->WriteHTML($template->render($variables));
            }
        }
        if (!$printWholeReportbook) {
            $this->mpdf->Output($this->outputDir . "/Berichte.pdf",'F');
        }
    }

    /**
     * @param string $userId
     * @param string $trainerTitle
     * @param string $trainerForename
     * @param string $trainerSurname
     * @param string $companyStreet
     * @param string $companyCity
     */
    public function printReportbook(string $userId, string $trainerTitle, string $trainerForename, string $trainerSurname, string $companyStreet, string $companyCity)
    {
        $this->printCover($userId, $trainerTitle, $trainerForename, $trainerSurname, $companyStreet, $companyCity, true);
        $this->printReports($userId, '', '', '', '', true);
        $this->mpdf->Output($this->outputDir . "/Berichtsheft.pdf",'F');
    }

    /**
     * @param string $text
     * @return int
     */
    public function countLines(string $text): int
    {
        $li = substr_count($text, "</li>");
        $br = substr_count($text, "<br>");
        $nl = substr_count($text, "\r\n");
        $p = substr_count($text, "</p>");

        return $li + $br + $nl + $p;
    }

    /**
     * @param array $reports
     * @return array
     */
    private function createArrayForStartAndEndOfWeek(array $reports): array
    {
        $weekInfoArr = [];
        foreach ($reports as $key => $report) {
            $weekInfo = $this->getStartAndEndDate($report->calendarWeek(), $report->calendarYear());
            $start = date("{$weekInfo[2]}.{$weekInfo[1]}.{$weekInfo[0]}");
            $end = date("{$weekInfo[5]}.{$weekInfo[4]}.{$weekInfo[3]}");
            $weekInfoArr[$key] = [
                $start,
                $end
            ];
        }

        return $weekInfoArr;
    }

    /**
     * @param array $reports
     * @param string $startMonth
     * @param string $startYear
     * @param string $endMonth
     * @param string $endYear
     * @return array
     */
    private function getReportsForPeriod(array $reports, string $startMonth, string $startYear, string $endMonth, string $endYear): array
    {
        $matchingReports = [];
        foreach ($reports as $report) {
            $reportMonthAndYear = $this->getStartAndEndDate($report->calendarWeek(), $report->calendarYear());
            $reportMonth = $reportMonthAndYear[1];
            $reportYear = $reportMonthAndYear[0];

            if ($reportYear >= $startYear && $reportYear <= $endYear) {
                if ($reportMonth >= $startMonth && $reportMonth <= $endMonth) {
                    $matchingReports[] = $report;
                }
            }
        }
        return $matchingReports;
    }

    /**
     * @param int $week
     * @param int $year
     * @return array
     */
    private function getStartAndEndDate(int $week, int $year): array
    {
        $date = new \DateTime();

        $date->setISODate($year, $week);

        $startYear = intVal($date->format('Y'));
        $startMonth = intVal($date->format('m'));
        $startDay = intVal($date->format('d'));

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
}