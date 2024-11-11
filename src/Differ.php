<?php

namespace Differ\Differ;

use function Differ\Formatters\formatSelect;
use function Differ\Parsers\fileParser;
use function Functional\sort;

function genDiff(mixed $file1Path, mixed $file2Path, string $formatName = 'stylish'): string
{
    $data1 = fileParser($file1Path);
    $data2 = fileParser($file2Path);
    if ($data1 !== false && $data2 !== false) {
        $dataDiff = arraysDiffer($data1, $data2);
        return formatSelect($dataDiff, $formatName);
    }
    return "Parsing of file(s) error!\n";
}

function getSortedKeys(array $data1, array $data2): array
{
    $keys1 = array_keys($data1);
    $keys2 = array_keys($data2);
    $unionKeys = array_merge($keys1, $keys2);
    $keys = array_unique($unionKeys);
    $keysSorted = sort($keys, fn ($left, $right) => $left <=> $right);
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

function arraysDiffer(mixed $data1, mixed $data2): mixed
{
    $sortedKeys = getSortedKeys($data1, $data2);
    $result = array_map(function ($key) use ($data1, $data2) {
        if (!array_key_exists($key, $data1)) {
            return ['type' => '+', 'key' => $key, 'value' => getValue($data2[$key])];
        } elseif (!array_key_exists($key, $data2)) {
            return ['type' => '-', 'key' => $key, 'value' => getValue($data1[$key])];
        } elseif (is_array($data1[$key]) && is_array($data2[$key])) {
            return ['type' => ' ', 'key' => $key, 'value' => arraysDiffer($data1[$key], $data2[$key])];
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
