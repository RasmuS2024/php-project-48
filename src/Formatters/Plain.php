<?php

namespace Differ\Formatters\Plain;

use function Functional\flatten;
use function Differ\Formatters\getStringValue;
use function Differ\Formatters\getStringFromArray;

function getLevel(string $level, string $key): string
{
    if ($level === '') {
        return $key;
    }
    return "{$level}.{$key}";
}

function getIterResult(mixed $valueComplex, string $value, string $property, bool $isArray = false): string
{
    if ($isArray) {
        $oldValue = '[complex value]';
        $newProperty = $property;
    } else {
        $oldValue = $value;
        $newProperty = "{$property}.{$valueComplex['key']}";
    }
    if (array_key_exists('new_value', $valueComplex)) {
        $newVal = getStringValue($valueComplex['new_value'], 'plain');
        if ($valueComplex['type'] === '_') {
            return "Property '{$newProperty}' was updated. From {$oldValue} to {$newVal}\n";
        }
    }
    $result = match ($valueComplex['type']) {
        ' ' => '',
        '+' => "Property '{$newProperty}' was added with value: {$oldValue}\n",
        '-' => "Property '{$newProperty}' was removed\n",
        default => '',
    };
    return $result;
}

function iter(mixed $value1, string $level = '', mixed $key1 = null): array
{
    $output = array_map(function ($key, $value) use ($level) {
        if (is_array($value) && array_key_exists('value', $value)) {
            if (is_array($value['value'])) {
                $newLevel = getLevel($level, $value['key']);
                $arrayResult = iter($value['value'], $newLevel, $value['key']);
                //var_dump($arrayResult);
                $stringResult = getStringFromArray($arrayResult);
                $result = getIterResult($value, $stringResult, $newLevel, true);
                return "{$result}{$stringResult}";
            } else {
                $stringResult = getStringValue($value['value'], 'plain');
                $result = getIterResult($value, $stringResult, $level);
            }
            return $result;
        }
    }, array_keys($value1), $value1);
    return $output;
}

function plain(array $tree): string
{
    $temp = iter($tree, '');
    $result = implode('', flatten($temp));
    return substr($result, 0, -1);
}
