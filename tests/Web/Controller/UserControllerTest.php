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
        $this->webDriver->get("{$this->url}/user");
        $this->assertContains('Berichtsheft', $this->webDriver->getTitle());
    }

    /**
     * @test
     */
    public function itShouldUploadImage()
    {
        $serializer = new Serializer();

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

        $fileInput->sendKeys('./tests/Web/Controller/test-picture.png')->submit();

        $user = $userRepository->findUserByUsername('admin');
        $profile = $profileRepository->findProfileByUserId($user->id());
        $baseOfProfilePicture = $profileRepository->findProfileByUserId($user->id())->image();
        $baseOfFile = base64_encode(file_get_contents('./tests/Web/Controller/test-picture.png'));

        $this->assertEquals($baseOfFile, $baseOfProfilePicture);
    }
}
