<?php

namespace GenDiff\Parsers;

use Symfony\Component\Yaml\Yaml;

function returnValue(array $json, string $key): mixed
{
    if (isset($json[$key])) {
        $value = $json[$key];
        if (is_bool($value)) {
            $value = ($value == 1) ? 'true' : 'false';
        }
        return $value;
    } else {
        return false;
    }
}

function getSortedKeys(array $json1, array $json2): array
{
    $keys1 = array_keys($json1);
    $keys2 = array_keys($json2);
    $keys = array_merge($keys1, $keys2);
    $keys = array_unique($keys);
    sort($keys, SORT_STRING);
    return $keys;
}

function fileReader(string $pathToFile): array
{
    $extension = pathinfo($pathToFile)['extension'];
    return match ($extension) {
        'json' => json_decode(file_get_contents($pathToFile), true),
        'yaml' => Yaml::parseFile($pathToFile),
        'yml' => Yaml::parseFile($pathToFile),
        default => '',
    };
}

function filesDiff(string $file1Path, string $file2Path, string $parsingFormat = 'json'): string
{
    $json1 = fileReader($file1Path);
    $json2 = fileReader($file2Path);
    $keys = getSortedKeys($json1, $json2);
    $res = "{\n";
    foreach ($keys as $key) {
        $value1 = returnValue($json1, $key);
        $value2 = returnValue($json2, $key);
        if ($value1) {
            if ($value2) {
                if ($value1 === $value2) {
                    $res = "{$res}    {$key}: {$value1}\n";
                } else {
                    $res = "{$res}  - {$key}: {$value1}\n";
                    $res = "{$res}  + {$key}: {$value2}\n";
                }
            } else {
                $res = "{$res}  - {$key}: {$value1}\n";
            }
        } else {
            if ($value2) {
                $res = "{$res}  + {$key}: {$value2}\n";
            }
        }
    }
    return "{$res}}";
}
