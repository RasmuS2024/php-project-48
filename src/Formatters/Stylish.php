<?php

namespace Differ\Formatters\Stylish;

use function Functional\flatten;

function getLevelSpaces(int $level)
{
    return str_repeat(' ', $level * 4 - 2);
}

function iter(mixed $value1, int $level = 1): array
{
    $output = array_map(function ($key, $value) use ($level) {
        $spaces = getLevelSpaces($level);
        if (is_array($value) && array_key_exists('value', $value)) {
            if (is_array($value['value'])) {
                $tkey = $value['key'];
                $type = $value['type'];
                $levelNew = $level + 1;
                $temp = iter($value['value'], $levelNew);
                $tempFlatten = flatten($temp);
                $tempImplode = implode('', $tempFlatten);
                if ($type === "_") {
                    $newValue = $value['new_value'];
                    $newVal = (is_string($newValue)) ? $newValue : json_encode($newValue);
                    return "{$spaces}- {$tkey}: {\n{$tempImplode}{$spaces}  }\n{$spaces}+ {$tkey}: {$newVal}\n";
                } else {
                    return "{$spaces}{$type} {$tkey}: {\n{$tempImplode}{$spaces}  }\n";
                }
            } else {
                $type = $value['type'];
                $tkey = $value['key'];
                $tempValue = (is_string($value['value'])) ? $value['value'] : json_encode($value['value']);
                $val = ($tempValue === '') ? ' ' : " {$tempValue}";
                if ($type === "_") {
                    $newValue = $value['new_value'];
                    $tempNewValue = (is_string($newValue)) ? $newValue : json_encode($newValue);
                    $newVal = ($tempNewValue === '') ? ' ' : " {$tempNewValue}";
                    return "{$spaces}- {$tkey}:{$val}\n{$spaces}+ {$tkey}:{$newVal}\n";
                } else {
                    return "{$spaces}{$type} {$tkey}:{$val}\n";
                }
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
