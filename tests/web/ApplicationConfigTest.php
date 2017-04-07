<?php

namespace Jimdo\Reports\Web;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;

class ApplicationConfigTest extends TestCase
{
    private $applicationEnvBackup;

    private $mongoUriBackup;

    public function setUp()
    {
        $this->applicationEnvBackup = getenv('APPLICATION_ENV');
        $this->mongoUriBackup = getenv('MONGO_URI');
    }

    public function tearDown()
    {
        putenv("APPLICATION_ENV=$this->applicationEnvBackup");
        putenv("MONGO_URI=$this->mongoUriBackup");
    }

    /**
     * @test
     */
    public function itShouldGetYamlStringFromConfig()
    {
        $appConfig = new ApplicationConfig(__DIR__ . '/../fixtures/config.yml');

        $env = 'development';
        putenv("APPLICATION_ENV={$env}");

        $testUri = $appConfig->mongoServerDb;

        $uri = 'reportbook_development';

        $this->assertEquals($uri, $testUri);
    }

    /**
     * @test
     */
    public function itShouldGetEnvStringFromBash()
    {
        $appConfig = new ApplicationConfig(__DIR__ . '/../fixtures/config.yml');

        $env = 'development';
        putenv("APPLICATION_ENV={$env}");

        $uri = 'mongo:192.168.178:2000';
        putenv("MONGO_URI={$uri}");

        $mongoUri = $appConfig->mongoUri;

        $this->assertEquals($mongoUri, $uri);
    }

    /**
     * @test
     */
    public function itShouldHavePath()
    {
        $env = 'development';
        putenv("APPLICATION_ENV={$env}");

        $path = realpath(__DIR__ . '/../fixtures/config.yml');

        $appConfig = new ApplicationConfig($path);

        $this->assertEquals('reportbook_development', $appConfig->mongoServerDb);
    }

    /**
     * @test
     * @expectedException Jimdo\Reports\Web\ApplicationConfigException
     */
    public function itShouldThrowExceptionIfVariableNotFound()
    {
        putenv('APPLICATION_ENV');
        $path = realpath(__DIR__ . '/../fixtures/config.yml');

        $appConfig = new ApplicationConfig($path);

        $appConfig->throwException;
    }
}
