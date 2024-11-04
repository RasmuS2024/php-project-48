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

function getSortedKeys(array $json1, array $json2): array
{
    $keys1 = array_keys($json1);
    $keys2 = array_keys($json2);
    $unionKeys = array_merge($keys1, $keys2);
    $keys = array_unique($unionKeys);
    $keysSorted = sort($keys, fn ($left, $right) => $left <=> $right);
    return $keysSorted;
}

function arrayKeysInsert(array $array): array
{
    $value1 = array_map(function ($keyIn, $valueIn) {
        if (!is_array($valueIn)) {
            return ['type' => ' ', 'key' => $keyIn, 'value' => $valueIn];
        } else {
            return ['type' => ' ', 'key' => $keyIn, 'value' => arrayKeysInsert($valueIn)];
        }
    }, array_keys($array), $array);
    return $value1;
}

function arraysDiffer(mixed $data1, mixed $data2): mixed
{
    $sortedKeys = getSortedKeys($data1, $data2);
    $data = array_map(function ($key) use ($data1, $data2) {
        if (!array_key_exists($key, $data1)) {
            if (is_array($data2[$key])) {
                $value2 = arrayKeysInsert($data2[$key]);
            } else {
                $value2 = $data2[$key];
            }
            return ['type' => '+', 'key' => $key, 'value' => $value2];
        } elseif (!array_key_exists($key, $data2)) {
            if (is_array($data1[$key])) {
                $value1 = arrayKeysInsert($data1[$key]);
            } else {
                $value1 = $data1[$key];
            }
            return ['type' => '-', 'key' => $key, 'value' => $value1];
        }
        $value1 = $data1[$key];
        $value2 = $data2[$key];
        if (is_array($value1) && is_array($value2)) {
            return ['type' => ' ', 'key' => $key, 'value' => arraysDiffer($value1, $value2)];
        }
        if ($value1 === $value2) {
            return  ['type' => ' ', 'key' => $key, 'value' => $value1];
        } else {
            if (is_array($value1)) {
                $value1New = arrayKeysInsert($value1);
            } else {
                $value1New = $value1;
            }
            if (is_array($value2)) {
                $value2New = arrayKeysInsert($value2);
            } else {
                $value2New = $value2;
            }
            return ['type' => '_', 'key' => $key, 'value' => $value1New, 'new_value' => $value2New];
        }
    }, $sortedKeys);
     return $data;
}
