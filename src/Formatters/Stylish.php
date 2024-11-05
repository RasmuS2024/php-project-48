<?php

namespace Differ\Formatters\Stylish;

use function Functional\flatten;
use function Differ\Formatters\getStringValue;

function getLevelSpaces(int $level)
{
    return str_repeat(' ', $level * 4 - 2);
}

function getIterResult(mixed $valueComplex, mixed $value, string $spaces): string
{
    $type = $valueComplex['type'];
    $key = $valueComplex['key'];
    if ($type === "_") {
        $newValue = getStringValue($valueComplex['new_value']);
        return "{$spaces}- {$key}: {$value}\n{$spaces}+ {$key}: {$newValue}\n";
    } else {
        return "{$spaces}{$type} {$key}: {$value}\n";
    }
}

function iter(mixed $value1, int $level = 1): array
{
    $output = array_map(function ($key, $value) use ($level) {
        $spaces = getLevelSpaces($level);
        if (is_array($value) && array_key_exists('value', $value)) {
            if (is_array($value['value'])) {
                $levelNew = $level + 1;
                $temp = iter($value['value'], $levelNew);
                $tempFlatten = flatten($temp);
                $tempImplode = implode('', $tempFlatten);
                $val = "{\n{$tempImplode}{$spaces}  }";
                return getIterResult($value, $val, $spaces);
            } else {
                $val = getStringValue($value['value']);
                return getIterResult($value, $val, $spaces);
            }
        }
    }, array_keys($value1), $value1);
    return $output;
}

function stylish(array $tree): string
{
    $temp1 = iter($tree, 1);
    $temp2 = flatten($temp1);
    $result = implode($temp2);
    return "{\n{$result}}";
}
