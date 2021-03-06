<?php

namespace Jimdo\Reports\stories;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\SerializerFactory;
use Jimdo\Reports\RepositoryFactory;

class ProfileFrontendTest extends TestCase
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
        $this->appConfig = new ApplicationConfig(__DIR__ . '/../../config.yml');
        $this->url = 'http://' . $this->appConfig->dockerIp . '/';

        // We have to look in the dev database because the server is running in dev environment
        $this->appEnvBackup = getenv('APPLICATION_ENV');
        putenv('APPLICATION_ENV=dev');
    }

    protected function tearDown()
    {
        putenv("APPLICATION_ENV={$this->appEnvBackup}");
        $this->webDriver->quit();
    }

    /**
     * @test
     */
    public function itShouldTestWithFirefox()
    {
        $capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'firefox');
        $this->webDriver = \RemoteWebDriver::create('http://' . $this->appConfig->dockerIp . ':4444/wd/hub', $capabilities);

        $this->pageTitle();
    }

    /**
     * @test
     */
    public function itShouldTestWithChrome()
    {
        $capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'chrome');
        $this->webDriver = \RemoteWebDriver::create('http://' . $this->appConfig->dockerIp . ':4444/wd/hub', $capabilities);

        $this->pageTitle();
        $this->uploadImage();
    }

    private function pageTitle()
    {
        $this->webDriver->get("{$this->url}/user");
        $this->assertContains('Berichtsheft', $this->webDriver->getTitle());
    }

    private function uploadImage()
    {
        $serializerFactory = new SerializerFactory($this->appConfig);
        $serializer = $serializerFactory->createSerializer();

        $repositoryFactory = new RepositoryFactory($this->appConfig, $serializer);
        $profileRepository = $repositoryFactory->createProfileRepository();
        $userRepository = $repositoryFactory->createUserRepository();

        // Login process
        $this->webDriver->get("{$this->url}/user");
        $username = $this->webDriver->findElement(\WebDriverBy::id('identifier'));
        $username->click();

        $this->webDriver->getKeyboard()->sendKeys('admin');
        $password = $this->webDriver->findElement(\WebDriverBy::id('password'));
        $password->click();

        $this->webDriver->getKeyboard()->sendKeys('Adminadmin123');
        $this->webDriver->getKeyboard()->pressKey(\WebDriverKeys::ENTER);

        // Upload Picture process
        $linkToProfile = $this->webDriver->findElement(\WebDriverBy::partialLinkText("Profil"));
        $linkToProfile->click();

        $editPicture = $this->webDriver->findElement(
            \WebDriverBy::cssSelector('div.profile-picture > a.glyphicon')
        );
        $editPicture->click();

        $fileInput = $this->webDriver->findElement(\WebDriverBy::id('fileToUpload'));
        $fileInput->setFileDetector(new \LocalFileDetector());
        $fileInput->sendKeys('./tests/stories/test-picture.png')->submit();

        $user = $userRepository->findUserByUsername('admin');
        $profile = $profileRepository->findProfileByUserId($user->id());
        $baseOfProfilePicture = $profileRepository->findProfileByUserId($user->id())->image();
        $baseOfFile = base64_encode(file_get_contents('./tests/stories/test-picture.png'));

        $this->assertEquals($baseOfFile, $baseOfProfilePicture);
    }
}
