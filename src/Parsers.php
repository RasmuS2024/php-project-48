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
                    $result = json_decode($fileContent, true);
                }
                break;
            case 'yaml':
            case 'yml':
                $result = Yaml::parseFile($pathToFile);
                break;
            default:
                break;
        }
    }
    return $result ?? false;
}
