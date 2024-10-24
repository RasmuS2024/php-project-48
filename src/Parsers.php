<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function fileParser(string $pathToFile): mixed
{
    $extension = pathinfo($pathToFile)['extension'];
    $fileContent = file_get_contents($pathToFile);
    if ($fileContent !== false) {
        return match ($extension) {
            'json' => json_decode($fileContent, true),
            'yaml' => Yaml::parseFile($pathToFile),
            'yml' => Yaml::parseFile($pathToFile),
            default => '',
        };
    } else {
        print_r("Error! - File '{$pathToFile}' not read.\n");
        return false;
    }
}
