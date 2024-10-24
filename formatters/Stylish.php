<?php

namespace Differ\Formatters\Stylish;

function getLevelSpaces(int $level)
{
    return str_repeat(' ', $level * 4 - 2);
}

function array_flatten($tree, $depth = 0)
{
    $result = [];
    foreach ($tree as $key => $value) {
        if ($depth >= 0 && is_array($value)) {
            $value = array_flatten($value, $depth > 1 ? $depth - 1 : 0 - $depth);
            $result = array_merge($result, $value);
        } else {
            $result[] = $value;
        }
    }
    return $result;
}

function iter(mixed$value1, $level = 1,  $key1 = null): array
{
    $output = array_map(function ($key, $value) use ($level) {
        if (is_array($value) && array_key_exists('value', $value)) {
            if (is_array($value['value'])) {
                $spaces = getLevelSpaces($level);
                $type = $value['type'];
                $tkey = $value['key'];
                $level++;
                $temp = iter($value['value'], $level, $tkey);
                $temp = array_flatten($temp);
                $temp = implode('', $temp);
                if ($type === "_") {
                    $newVal = $value['new_value'];
                    $newVal = (is_string($newVal)) ? $newVal : json_encode($newVal);
                    return "{$spaces}- {$tkey}: {\n{$temp}{$spaces}  }\n{$spaces}+ {$tkey}: {$newVal}\n";
                } else {
                    return "{$spaces}{$type} {$tkey}: {\n{$temp}{$spaces}  }\n";
                }
            } else {
                $spaces = getLevelSpaces($level);
                $type = $value['type'];
                $tkey = $value['key'];
                $val = $value['value'];
                $val = (is_string($val)) ? "$val" : json_encode($val);
                $val = ($val === '') ? ' ' : " {$val}";
                if ($type === "_") {
                    $newVal = $value['new_value'];
                    $newVal = (is_string($newVal)) ? $newVal : json_encode($newVal);
                    $newVal = ($newVal === '') ? ' ' : " {$newVal}";
                    return "{$spaces}- {$tkey}:{$val}\n{$spaces}+ {$tkey}:{$newVal}\n";
                } else {
                    return "{$spaces}{$type} {$tkey}:{$val}\n";
                }
            }
        }
    }, array_keys($value1), $value1);
    return $output;
}

function stylish(array $tree)
{
    $result[] = "{\n";
    $result[] = iter($tree, 1);
    $result[] = "}";
    return implode('', array_flatten($result));
}
