<?php

namespace GenDiff\Parsers;

use Symfony\Component\Yaml\Yaml;

function returnValue(array $json, string $key): mixed
{
    
    if (array_key_exists($key, $json)) {
        $value = $json[$key];
        if ($value === NULL) {
            return 'null';
        }
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

function filesDiff(mixed $file1Path, mixed $file2Path): mixed
{
    $data1 = fileReader($file1Path);
    $data2 = fileReader($file2Path);
    return json_encode(filesDiffer($data1, $data2));

}

function filesDiffer(mixed $data1, mixed $data2): mixed
{
    $sortedKeys = getSortedKeys($data1, $data2);
    $data = array_map(function ($key) use ($data1, $data2) {
        if (!array_key_exists($key, $data1)) {
            return ['type' => '+', $key => $data2[$key]];
        } elseif (!array_key_exists($key, $data2)) {
            return ['type' => '-', $key => $data1[$key]];
        }
        $value1 = $data1[$key];
        $value2 = $data2[$key];
        if (is_array($value1) && is_array($value2)) {
            //return filesDiffer($value1, $value2);
            return ['type' => ' ', $key => filesDiffer($value1, $value2)];

        }
        if ($value1 === $value2) {
            return  ['type' => ' ', $key => $value1];
        } 
        else {
            return ['type' => '_', $key => $value1, 'new_value' => $value2];
        }

    }, $sortedKeys);
     return $data;
}    
