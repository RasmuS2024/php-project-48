<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function fileParser(string $pathToFile): mixed
{
    $pathToFileElements = pathinfo($pathToFile);
    if (array_key_exists('extension', $pathToFileElements)) {
        $extension = $pathToFileElements['extension'];
        $fileContent = file_get_contents($pathToFile);
        if ($fileContent !== false) {
            return match ($extension) {
                'json' => json_decode($fileContent, true),
                'yaml' => Yaml::parseFile($pathToFile),
                'yml' => Yaml::parseFile($pathToFile),
                default => '',
            };
        } else {
            return false;
        }
    } else {
        return false;
    }
}
