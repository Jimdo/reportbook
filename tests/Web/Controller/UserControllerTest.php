<?php

namespace Jimdo\Web\Controller;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\Serializer;
use Jimdo\Reports\RepositoryFactory;

class UserControllerTest extends TestCase
{
    /** @var string */
    private $appEnvBackup;

    /** @var ApplicationConfig */
    private $appConfig;

    /** @var \RemoteWebDriver */
    private $webDriver;

    /** @var string */
    private $url;

	protected function setUp()
    {
        $this->appConfig = new ApplicationConfig(__DIR__ . '/../../../config.yml');

        echo 'xxx' . $this->appConfig->reportbookIp . 'xxx';
        $capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'chrome');
        $this->webDriver = \RemoteWebDriver::create('http://' . $this->appConfig->seleniumIp . ':4444/wd/hub', $capabilities);
        
        $this->url = 'http://' . $this->appConfig->reportbookIp . '/';

        // We have to look in the dev database because the server is running in dev environment
        $this->appEnvBackup = getenv('APPLICATION_ENV');
        putenv('APPLICATION_ENV=dev');
    }

    protected function tearDown()
    {
        putenv($this->appEnvBackup);
        $this->webDriver->quit();
    }

    /**
     * @test
     */
    public function itShouldTestPageTitle()
    {
        $this->webDriver->get("https://github.com");
        $this->assertContains('GitHub', $this->webDriver->getTitle());
    }
}