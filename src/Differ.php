<?php

namespace Differ\Differ;

use function Differ\Formatters\getFormattedDiff;
use function Differ\Parsers\parseDataWithFormat;
use function Functional\sort;

const ALLOWED_FILE_TYPES = ['json', 'yaml', 'yml'];

function getDataFromFile(string $pathToFile): array
{
    if (!file_exists($pathToFile)) {
        throw new \RuntimeException("File '{$pathToFile}' not found.");
    }
    $fileExtension = array_key_exists('extension', pathinfo($pathToFile)) ? pathinfo($pathToFile)['extension'] : false;
    if (!in_array($fileExtension, ALLOWED_FILE_TYPES, true)) {
        $allowedFileTypesString = implode("', '", ALLOWED_FILE_TYPES);
        throw new \RuntimeException("File extension is not allowed! Allowed types: '{$allowedFileTypesString}'.");
    }
    $fileContents = file_get_contents($pathToFile);
    if ($fileContents === false) {
        throw new \RuntimeException("Unable to read file {$pathToFile}.");
    }
    return ['extension' => $fileExtension, 'data' => $fileContents];
}

function genDiff(string $file1Path, string $file2Path, string $formatName = 'stylish'): string
{
    $dataFromFile1 = getDataFromFile($file1Path);
    $dataFromFile2 = getDataFromFile($file2Path);
    $parsedData1 = parseDataWithFormat($dataFromFile1);
    $parsedData2 = parseDataWithFormat($dataFromFile2);
    $dataDiff = getArraysDiffer($parsedData1, $parsedData2);
    return getFormattedDiff($dataDiff, $formatName);
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
        return array_map(function ($keyIn, $valueIn) {
            if (!is_array($valueIn)) {
                return ['type' => ' ', 'key' => $keyIn, 'value' => $valueIn];
            }
            return ['type' => ' ', 'key' => $keyIn, 'value' => getValue($valueIn)];
        }, array_keys($inValue), $inValue);
    }
    return $inValue;
}

function getArraysDiffer(mixed $data1, mixed $data2): mixed
{
    $sortedKeys = getSortedKeys($data1, $data2);
    return array_map(function ($key) use ($data1, $data2) {
        if (!array_key_exists($key, $data1)) {
            return ['type' => '+', 'key' => $key, 'value' => getValue($data2[$key])];
        }
        if (!array_key_exists($key, $data2)) {
            return ['type' => '-', 'key' => $key, 'value' => getValue($data1[$key])];
        }
        if (is_array($data1[$key]) && is_array($data2[$key])) {
            return ['type' => ' ', 'key' => $key, 'value' => getArraysDiffer($data1[$key], $data2[$key])];
        }
        if ($data1[$key] === $data2[$key]) {
            return ['type' => ' ', 'key' => $key, 'value' => $data1[$key]];
        }
        $value = getValue($data1[$key]);
        $newValue = getValue($data2[$key]);
        return ['type' => '_', 'key' => $key, 'value' => $value, 'new_value' => $newValue];
    }, $sortedKeys);
}
