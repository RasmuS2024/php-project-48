<?php

namespace Differ\Differ;

use function Differ\Formatters\getFormattedDiff;
use function Differ\Parsers\parseDataWithFormat;
use function Functional\sort;

const ALLOWED_FILE_TYPES = ['json', 'yaml', 'yml'];

function getDataFromFile(string $pathToFile): array
{
    if (!file_exists($pathToFile)) {
        return ['success' => false, 'error' => "File '{$pathToFile}' not found."];
    }
    $fileExtension = array_key_exists('extension', pathinfo($pathToFile)) ? pathinfo($pathToFile)['extension'] : false;
    if (!in_array($fileExtension, ALLOWED_FILE_TYPES, true)) {
        $allowedFileTypesString = implode("', '", ALLOWED_FILE_TYPES);
        return [
            'success' => false,
            'error' => "File extension is not allowed! Allowed types: '{$allowedFileTypesString}'."
        ];
    }
    $fileContents = file_get_contents($pathToFile);
    if ($fileContents === false) {
        return ['success' => false, 'error' => "Unable to read file {$pathToFile}."];
    }
    return ['success' => true, 'extension' => $fileExtension, 'data' => $fileContents];
}

function getParsedData(string $pathToFile): array
{
    $dataFromFile = getDataFromFile($pathToFile);
    if ($dataFromFile['success']) {
        $parsedData = parseDataWithFormat($dataFromFile['data'], $dataFromFile['extension']);
        if (!$parsedData['success']) {
            return ['success' => false, 'error' => "Unable to parse the file {$pathToFile}"];
        }
    } else {
        return ['success' => false, 'error' => $dataFromFile['error']];
    }
    return ['success' => true, 'data' => $parsedData['data']];
}

function genDiff(mixed $file1Path, mixed $file2Path, string $formatName = 'stylish'): string
{
    $parsedData1 = getParsedData($file1Path);
    if (!$parsedData1['success']) {
        return "Error: {$parsedData1['error']}";
    }
    $parsedData2 = getParsedData($file2Path);
    if (!$parsedData2['success']) {
        return "Error: {$parsedData2['error']}";
    }
    $dataDiff = getArraysDiffer($parsedData1['data'], $parsedData2['data']);
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
