<?php

namespace Differ\Formatters\Plain;

use function Functional\flatten;

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
        $tempValue = $valueComplex['new_value'];
        $newVal = is_string($tempValue) ? "'{$tempValue}'" : json_encode($tempValue);
        if ($valueComplex['type'] === 'changed') {
            return "Property '{$newProperty}' was updated. From {$oldValue} to {$newVal}\n";
        }
    }
    $result = match ($valueComplex['type']) {
        'added' => "Property '{$newProperty}' was added with value: {$oldValue}\n",
        'deleted' => "Property '{$newProperty}' was removed\n",
        default => '',
    };
    return $result;
}

function iter(mixed $value1, string $level = ''): array
{
    $output = array_map(function ($key, $value) use ($level) {
        if (is_array($value) && array_key_exists('value', $value)) {
            if (is_array($value['value'])) {
                $newLevel = getLevel($level, $value['key']);
                $arrayResult = iter($value['value'], $newLevel);
                $stringResult = implode('', flatten($arrayResult));
                $result = getIterResult($value, $stringResult, $newLevel, true);
                return "{$result}{$stringResult}";
            } else {
                $stringResult = is_string($value['value']) ? "'{$value['value']}'" : json_encode($value['value']);
                $result = getIterResult($value, "{$stringResult}", $level);
            }
            return $result;
        }
    }, array_keys($value1), $value1);
    return $output;
}

function getPlainFormat(array $tree): string
{
    $temp = iter($tree, '');
    $result = implode('', flatten($temp));
    return substr($result, 0, -1);
}
