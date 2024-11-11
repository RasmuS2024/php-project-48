<?php

namespace Differ\Differ;

use function Differ\Formatters\getFormattedDiff;
use function Differ\Parsers\parseDataWithFormat;
use function Functional\sort;

function getDataFromFile(string $pathToFile): mixed
{
    $fileContent = file_get_contents($pathToFile);
    return $fileContent;
}

function getFileExtension(string $pathToFile): string
{
    return pathinfo($pathToFile)['extension'] ?? '';
}

function genDiff(mixed $file1Path, mixed $file2Path, string $formatName = 'stylish'): string
{
    $dataFromFile1 = getDataFromFile($file1Path);
    $dataFromFile2 = getDataFromFile($file2Path);
    if ($dataFromFile1 !== false && $dataFromFile2 !== false) {
        $parsedData1 = parseDataWithFormat($dataFromFile1, getFileExtension($file1Path));
        $parsedData2 = parseDataWithFormat($dataFromFile2, getFileExtension($file2Path));
        $dataDiff = getArraysDiffer($parsedData1, $parsedData2);
        return getFormattedDiff($dataDiff, $formatName);
    }
    return "Reading of file(s) error!\n";
}

function getSortedKeys(array $data1, array $data2): array
{
    $keys1 = array_keys($data1);
    $keys2 = array_keys($data2);
    $unionKeys = array_merge($keys1, $keys2);
    $uniqueKeys = array_unique($unionKeys);
    $keysSorted = sort($uniqueKeys, fn ($left, $right) => $left <=> $right);
    return $keysSorted;
}

function getValue(mixed $inValue): mixed
{
    if (is_array($inValue)) {
        $result = array_map(function ($keyIn, $valueIn) {
            if (!is_array($valueIn)) {
                return ['type' => ' ', 'key' => $keyIn, 'value' => $valueIn];
            } else {
                return ['type' => ' ', 'key' => $keyIn, 'value' => getValue($valueIn)];
            }
        }, array_keys($inValue), $inValue);
        return $result;
    }
    return $inValue;
}

function getArraysDiffer(mixed $data1, mixed $data2): mixed
{
    $sortedKeys = getSortedKeys($data1, $data2);
    $result = array_map(function ($key) use ($data1, $data2) {
        if (!array_key_exists($key, $data1)) {
            return ['type' => '+', 'key' => $key, 'value' => getValue($data2[$key])];
        } elseif (!array_key_exists($key, $data2)) {
            return ['type' => '-', 'key' => $key, 'value' => getValue($data1[$key])];
        } elseif (is_array($data1[$key]) && is_array($data2[$key])) {
            return ['type' => ' ', 'key' => $key, 'value' => getArraysDiffer($data1[$key], $data2[$key])];
        } elseif ($data1[$key] === $data2[$key]) {
            return ['type' => ' ', 'key' => $key, 'value' => $data1[$key]];
        } else {
            $value = getValue($data1[$key]);
            $newValue = getValue($data2[$key]);
            return ['type' => '_', 'key' => $key, 'value' => $value, 'new_value' => $newValue];
        }
    }, $sortedKeys);
    return $result;
}
