<?php

namespace Differ\Formatters\Plain;

use function Differ\Formatters\Stylish\array_flatten;

function iter(mixed $value1, string $level = '', mixed $key1 = null): array
{
    $output = array_map(function ($key, $value) use ($level) {
        if (is_array($value) && array_key_exists('value', $value)) {
            if (is_array($value['value'])) {
                $type = $value['type'];
                $tkey = $value['key'];
                if ($level === '') {
                    $level = $tkey;
                } else {
                    $level = "{$level}.{$tkey}";
                }
                $arrayResult = iter($value['value'], $level, $tkey);
                $arrayResult2 = array_flatten($arrayResult);
                $arrayResult3 = implode('', $arrayResult2);
                if (array_key_exists('new_value', $value)) {
                    $newVal = $value['new_value'];
                    if (is_string($newVal)) {
                        $newVal = "'{$newVal}'";
                    } else {
                        $newVal = json_encode($newVal);
                    }
                }
                $result = match ($type) {
                    ' ' => '',
                    '+' => "Property '{$level}' was added with value: [complex value]\n",
                    '-' => "Property '{$level}' was removed\n",
                    '_' => "Property '{$level}' was updated. From [complex value] to {$newVal}\n",
                };
                return "{$result}{$arrayResult3}";
            } else {
                $type = $value['type'];
                $tkey = $value['key'];
                $val = $value['value'];
                $val = (is_string($val)) ? "'{$val}'" : json_encode($val);
                if (array_key_exists('new_value', $value)) {
                    $newVal = $value['new_value'];
                    $newVal = (is_string($newVal)) ? "'{$newVal}'" : json_encode($newVal);
                }
                $result = match ($type) {
                    ' ' => '',
                    '+' => "Property '{$level}.{$tkey}' was added with value: {$val}\n",
                    '-' => "Property '{$level}.{$tkey}' was removed\n",
                    '_' => "Property '{$level}.{$tkey}' was updated. From {$val} to {$newVal}\n",
                };
            }
            return $result;
        }
    }, array_keys($value1), $value1);
    return $output;
}

function plain(array $tree): string
{
    $result = iter($tree, '');
    $result = implode('', array_flatten($result));
    return substr($result, 0, -1);
}
