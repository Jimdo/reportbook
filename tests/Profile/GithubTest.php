<?php

namespace Jimdo\Reports\Profile;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Web\ApplicationConfig;

class GitHubTest extends TestCase
{
    /**
     * @var \RemoteWebDriver
     */
    protected $webDriver;

	public function setUp()
    {
        $appConfig = new ApplicationConfig(__DIR__ . '/../../config.yml');
        $capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'firefox');
        $this->webDriver = \RemoteWebDriver::create('http://' . $appConfig->seleniumIp . ':4444/wd/hub', $capabilities);
    }

    public function tearDown()
    {
        $this->webDriver->quit();
    }

    protected $url = 'https://github.com';

    public function testGitHubHome()
    {
        $this->webDriver->get($this->url);
        // checking that page title contains word 'GitHub'
        $this->assertContains('GitHub', $this->webDriver->getTitle());
    }    

    public function testSearch()
    {
        $this->webDriver->get($this->url);
        // find search field by its id
        $search = $this->webDriver->findElement(\WebDriverBy::id('user[login]'));
        $search->click();
	}    
}

