<?php

namespace Differ\Formatters\Stylish;

use function Functional\flatten;

function getLevelSpaces(int $level): string
{
    return str_repeat(' ', $level * 4 - 2);
}

function getIterResult(mixed $valueComplex, mixed $value, string $spaces): string
{
    $type = $valueComplex['type'];
    $key = $valueComplex['key'];
    if ($type === "changed") {
        $tempValue = $valueComplex['new_value'];
        $newValue = is_string($tempValue) ? $tempValue : json_encode($tempValue);
        return "{$spaces}- {$key}: {$value}\n{$spaces}+ {$key}: {$newValue}\n";
    }
    $typeSymbol = match ($type) {
        'added' => '+',
        'deleted' => '-',
        default => ' ',
    };
    return "{$spaces}{$typeSymbol} {$key}: {$value}\n";
}

function iter(mixed $value1, int $level = 1): array
{
    return array_map(function ($key, $value) use ($level) {
        $spaces = getLevelSpaces($level);
        if (is_array($value) && array_key_exists('value', $value)) {
            if (is_array($value['value'])) {
                $levelNew = $level + 1;
                $arrayResult = iter($value['value'], $levelNew);
                $stringResult = implode('', flatten($arrayResult));
                $val = "{\n{$stringResult}{$spaces}  }";
                return getIterResult($value, $val, $spaces);
            }
            $val = is_string($value['value']) ? $value['value'] : json_encode($value['value']);
            return getIterResult($value, $val, $spaces);
        }
    }, array_keys($value1), $value1);
}

function getStylishFormat(array $tree): string
{
    $temp = iter($tree, 1);
    $result = implode('', flatten($temp));
    return "{\n{$result}}";
}
