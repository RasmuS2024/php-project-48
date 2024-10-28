<?php

namespace Differ\Formatters\Plain;

use function Functional\flatten;

function iter(mixed $value1, string $level = '', mixed $key1 = null): array
{
    $output = array_map(function ($key, $value) use ($level) {
        if (is_array($value) && array_key_exists('value', $value)) {
            if (is_array($value['value'])) {
                $type = $value['type'];
                $tkey = $value['key'];
                if ($level === '') {
                    $newLevel = $tkey;
                } else {
                    $newLevel = "{$level}.{$tkey}";
                }
                $arrayResult = iter($value['value'], $newLevel, $tkey);
                $arrayResult2 = flatten($arrayResult);
                $arrayResult3 = implode('', $arrayResult2);
                if (array_key_exists('new_value', $value)) {
                    if (is_string($value['new_value'])) {
                        $newVal = "'{$value['new_value']}'";
                    } else {
                        $newVal = json_encode($value['new_value']);
                    }
                    if ($type === '_') {
                        $temp = "Property '{$newLevel}' was updated. From [complex value] to {$newVal}\n";
                        return "{$temp}{$arrayResult3}";
                    }
                }
                $result = match ($type) {
                    ' ' => '',
                    '+' => "Property '{$newLevel}' was added with value: [complex value]\n",
                    '-' => "Property '{$newLevel}' was removed\n",
                    default => '',
                };
                return "{$result}{$arrayResult3}";
            } else {
                $type = $value['type'];
                $tkey = $value['key'];
                $val = (is_string($value['value'])) ? "'{$value['value']}'" : json_encode($value['value']);
                if (array_key_exists('new_value', $value)) {
                    $newVal = (is_string($value['new_value'])) ?
                    "'{$value['new_value']}'" :
                    json_encode($value['new_value']);
                    if ($type === '_') {
                        return "Property '{$level}.{$tkey}' was updated. From {$val} to {$newVal}\n";
                    }
                }
                $result = match ($type) {
                    ' ' => '',
                    '+' => "Property '{$level}.{$tkey}' was added with value: {$val}\n",
                    '-' => "Property '{$level}.{$tkey}' was removed\n",
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
