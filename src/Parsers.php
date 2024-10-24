<?php

namespace GenDiff\Parsers;

use Symfony\Component\Yaml\Yaml;

function fileParser(string $pathToFile): array
{
    $extension = pathinfo($pathToFile)['extension'];
    return match ($extension) {
        'json' => json_decode(file_get_contents($pathToFile), true),
        'yaml' => Yaml::parseFile($pathToFile),
        'yml' => Yaml::parseFile($pathToFile),
        default => '',
    };
}
