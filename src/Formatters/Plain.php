<?php

namespace Differ\Formatters\Plain;

use function Functional\flatten;
use function Differ\Formatters\getStringValue;

function getLevel(string $level, string $key): string
{
    if ($level === '') {
        return $key;
    }
    return "{$level}.{$key}";
}

function iter(mixed $value1, string $level = '', mixed $key1 = null): array
{
    $output = array_map(function ($key, $value) use ($level) {
        if (is_array($value) && array_key_exists('value', $value)) {
            if (is_array($value['value'])) {
                $newLevel = getLevel($level, $value['key']);
                $arrayResult = iter($value['value'], $newLevel, $value['key']);
                $arrayResult2 = flatten($arrayResult);
                $arrayResult3 = implode('', $arrayResult2);
                if (array_key_exists('new_value', $value)) {
                    $newVal = getStringValue($value['new_value'], 'plain');
                    if ($value['type'] === '_') {
                        $temp = "Property '{$newLevel}' was updated. From [complex value] to {$newVal}\n";
                        return "{$temp}{$arrayResult3}";
                    }
                }
                $result = match ($value['type']) {
                    ' ' => '',
                    '+' => "Property '{$newLevel}' was added with value: [complex value]\n",
                    '-' => "Property '{$newLevel}' was removed\n",
                    default => '',
                };
                return "{$result}{$arrayResult3}";
            } else {
                $val = getStringValue($value['value'], 'plain');
                if (array_key_exists('new_value', $value)) {
                    $newVal = getStringValue($value['new_value'], 'plain');
                    if ($value['type'] === '_') {
                        return "Property '{$level}.{$value['key']}' was updated. From {$val} to {$newVal}\n";
                    }
                }
                $result = match ($value['type']) {
                    ' ' => '',
                    '+' => "Property '{$level}.{$value['key']}' was added with value: {$val}\n",
                    '-' => "Property '{$level}.{$value['key']}' was removed\n",
                    default => '',
                };
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
