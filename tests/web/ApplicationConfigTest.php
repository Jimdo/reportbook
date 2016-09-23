<?php

namespace Jimdo\Reports\Web;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;

class ApplicationConfigTest extends TestCase
{
    public function tearDown()
    {
        putenv("APPLICATION_ENV");
        putenv("MONGO_URI");
    }

    /**
     * @test
     */
    public function itShouldGetYamlStringFromConfig()
    {
        $appConfig = new ApplicationConfig(__DIR__ . '/../fixtures/config.yml');

        $env = 'development';
        putenv("APPLICATION_ENV={$env}");

        $testUri = $appConfig->mongoDb;

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

        $this->assertEquals('reportbook_development', $appConfig->mongoDb);
    }

    /**
     * @test
     * @expectedException Jimdo\Reports\Web\ApplicationConfigException
     */
    public function itShouldThrowExceptionIfVariableNotFound()
    {
        $path = realpath(__DIR__ . '/../fixtures/config.yml');

        $appConfig = new ApplicationConfig($path);

        $appConfig->thowException;
    }
}
