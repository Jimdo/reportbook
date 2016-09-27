<?php

namespace Jimdo\Reports\Web;

use Symfony\Component\Yaml\Yaml;

class ApplicationConfig
{
    /** @var string */
    private $yml;

    /** @var string */
    private $path;

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->yml = $this->readYaml();
    }

    /**
     * @param string $key
     * @return string
     */
    public function __get(string $key): string
    {
        $env = getenv('APPLICATION_ENV');

        if ($env === false) {
            throw new \Jimdo\Reports\Web\ApplicationConfigException('No value found!');
        }

        $envString = getenv($this->envString($key));
        $ymlString;
        if (isset($this->yml[$env][$this->yamlString($key)])) {
            $ymlString = $this->yml[$env][$this->yamlString($key)];
        }

        if ($envString !== false) {
            return $envString;
        }

        if (isset($ymlString)) {
            return $ymlString;
        }
    }

    /**
     * @return array
     */
    private function readYaml(): array
    {
        return $this->yml = Yaml::parse(file_get_contents($this->path));
    }

    /**
     * @param string $camelString
     * @return string
     */
    private function envString(string $camelString): string
    {
        $lastCharacterUpper = ctype_upper($camelString[0]);
        for ($i=0; $i < strlen($camelString); $i++) {
            if (ctype_upper($camelString[$i]) === false || $lastCharacterUpper === true) {
                $lastCharacterUpper = false;
                continue;
            }

            $lastCharacterUpper = true;
            $camelString = substr_replace($camelString, '_', $i, 0);
            $i++;
        }
        return strtoupper($camelString);
    }

    /**
     * @param string $camelString
     * @return string
     */
    private function yamlString(string $camelString)
    {
        return strtolower($this->envString($camelString));
    }
}
