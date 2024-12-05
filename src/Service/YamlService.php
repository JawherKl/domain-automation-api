<?php

namespace App\Service;

use Symfony\Component\Yaml\Yaml;

class YamlService
{
    public function emitYaml(array $data): string
    {
        // Utilisation de Symfony pour générer du YAML
        return Yaml::dump($data);
    }
}
