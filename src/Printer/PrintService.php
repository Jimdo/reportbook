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

    /**
     * @param ProfileService
     * @param ReportbookService
     * @param ApplicationConfig
     */
    public function __construct(ProfileService $profileService, ReportbookService $reportService, ApplicationConfig $appConfig)
    {
        $this->twig = $twig;
        $this->appConfig = $appConfig;
        $this->reportService = $reportService;
        $this->profileService = $profileService;
        $this->mpdf = new \mPDF();

        $loader = new \Twig_Loader_Filesystem($this->appConfig->printerTemplates);
        $this->twig = new \Twig_Environment($loader);
    }

    /**
       * @param string $userId
       * @param string $trainerTitle
       * @param string $trainerForename
       * @param string $trainerSurname
       * @param string $companyStreet
       * @param string $companyCity
       */
      public function printCover(string $userId, string $trainerTitle, string $trainerForename, string $trainerSurname, string $companyStreet, string $companyCity)
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
          $this->mpdf->Output($this->appConfig->printerOutput . '/Deckblatt.pdf','F');
      }

      /**
       * @param string $userId
       * @param string $startMonth
       * @param string $startYear
       * @param string $endMonth
       * @param string $endYear
       */
      public function printReports(string $userId, string $startMonth, string $startYear, string $endMonth, string $endYear)
      {
          $profile = $this->profileService->findProfileByUserId($userId);
          $reports = $this->reportService->findByTraineeId($userId);
          $reports = $this->getReportsForPeriod($reports, $startMonth, $startYear, $endMonth, $endYear);

          $maxLinesPerSite = 25;
          $reportsPerSite = [];
          $site = 0;
          $lines = 0;

          foreach ($reports as $report) {
              $currentLines = $this->countLines($report->content());

              if ($lines + $currentLines <= $maxLinesPerSite) {
                  $lines += $currentLines;
              } else {
                  $lines = $currentLines;
                  $site++;
              }

              if (!isset($reportsPerSite[$site])) {
                  $reportsPerSite[$site] = [];
              }
              $reportsPerSite[$site][] = $report;
          }

          $template = $this->twig->load('Report.html');

          foreach ($reportsPerSite as $run => $site) {

              $variables = [
                  'traineeForename' => $profile->forename(),
                  'traineeSurname' => $profile->surname(),
                  'jobTitle' => $profile->jobTitle(),
                  'month' => 'Januar',
                  'year' => '2017',
                  'reports' => $site
              ];

              $this->mpdf->WriteHTML($template->render($variables));

              if (count($reportsPerSite) - 1 !== $run) {
                  $this->mpdf->AddPage();
              }
          }

          $this->mpdf->Output($this->appConfig->printerOutput . "/Berichte.pdf",'F');
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

          return [
              $startYear,
              $startMonth
          ];
      }
}
