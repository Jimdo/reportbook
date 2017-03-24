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
}
