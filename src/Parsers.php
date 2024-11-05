<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function fileParser(string $pathToFile): mixed
{
    $pathToFileElements = pathinfo($pathToFile);
    if (array_key_exists('extension', $pathToFileElements)) {
        $extension = $pathToFileElements['extension'];
        switch ($extension) {
            case 'json':
                $fileContent = file_get_contents($pathToFile);
                if ($fileContent !== false) {
                    return json_decode($fileContent, true);
                }
                return false;
            case 'yaml':
                return Yaml::parseFile($pathToFile);
            case 'yml':
                return Yaml::parseFile($pathToFile);
            default:
        }
    }
    return false;
}
