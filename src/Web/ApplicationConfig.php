<?php

namespace Jimdo\Reports\Web;

use Symfony\Component\Yaml\Yaml;

class ApplicationConfig
{
    private $yml;

    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->yml = $this->readYaml();
    }

        /**
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
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

    private function readYaml()
    {
        return $this->yml = Yaml::parse(file_get_contents($this->path));
    }

    private function envString(string $camelString)
    {
        $lastCharacterUpper = ctype_upper($camelString[0]);
        for ($i=0; $i < strlen($camelString); $i++) {
            if (ctype_upper($camelString[$i]) === false || $lastCharacterUpper === true) {
                $lastCharacterUpper = false;
                continue;
            }

            $lastCharacterUpper = true;
            $camelString = substr_replace($camelString , '_', $i,0);
            $i++;
        }
        return strtoupper($camelString);
    }

    private function yamlString(string $camelString)
    {
        return strtolower($this->envString($camelString));
    }
}
