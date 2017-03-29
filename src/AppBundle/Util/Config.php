<?php

namespace AppBundle\Util;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Filesystem\Filesystem;

class Config 
{
    private $parameters_yml_path;

    public function __construct($path) {
        $this->parameters_yml_path = $path;
    }

    public function parseConfig() {
        return Yaml::parse(file_get_contents($this->parameters_yml_path));
    }

    public function updateConfig($key, $value) {
        $parameters = $this->parseConfig();

        $parameters['parameters'][$key] = $value;

        $this->saveConfig($parameters);
    }

    public function saveConfig($parameters) {
        $new_yaml_string = Yaml::dump($parameters);

        $fs = new Filesystem();
        $fs->dumpFile($this->parameters_yml_path, $new_yaml_string);
    }
}
