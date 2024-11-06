<?php

namespace Differ\Formatters\Stylish;

use function Functional\flatten;
use function Differ\Formatters\getStringValue;
use function Differ\Formatters\getStringFromArray;

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
                $arrayResult = iter($value['value'], $levelNew);
                $stringResult = getStringFromArray($arrayResult);
                $val = "{\n{$stringResult}{$spaces}  }";
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
    $temp = iter($tree, 1);
    $result = getStringFromArray($temp);
    return "{\n{$result}}";
}
