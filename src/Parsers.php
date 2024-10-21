<?php

namespace GenDiff\Parsers;

use Symfony\Component\Yaml\Yaml;

function getSortedKeys(array $json1, array $json2): array
{
    $keys1 = array_keys($json1);
    $keys2 = array_keys($json2);
    $keys = array_merge($keys1, $keys2);
    $keys = array_unique($keys);
    sort($keys, SORT_STRING);
    return $keys;
}

function fileReader(string $pathToFile): array
{
    $extension = pathinfo($pathToFile)['extension'];
    return match ($extension) {
        'json' => json_decode(file_get_contents($pathToFile), true),
        'yaml' => Yaml::parseFile($pathToFile),
        'yml' => Yaml::parseFile($pathToFile),
        default => '',
    };
}

function genDiff(mixed $file1Path, mixed $file2Path, string $style = 'stylish'): mixed
{
    $data1 = fileReader($file1Path);
    $data2 = fileReader($file2Path);
    $tt = filesDiffer($data1, $data2);
    $st = '';
    if ($style === 'stylish') {
        $st = stylish($tt);
    }
    //print_r($st);
    return $st;
    //json_encode(filesDiffer($data1, $data2));
}

function arrayKeysInsert(array $array)
{
    $value1 = array_map(function ($keyIn, $valueIn) {
        if (!is_array($valueIn)) {
            return ['type' => ' ', 'key' => $keyIn, 'value' => $valueIn];
        } else {
            return ['type' => ' ', 'key' => $keyIn, 'value' => arrayKeysInsert($valueIn)];
        }
    }, array_keys($array), $array);
    return $value1;
}

function filesDiffer(mixed $data1, mixed $data2): mixed
{
    $sortedKeys = getSortedKeys($data1, $data2);
    $data = array_map(function ($key) use ($data1, $data2) {
        if (!array_key_exists($key, $data1)) {
            if (is_array($data2[$key])) {
                $value2 = arrayKeysInsert($data2[$key]);
            } else {
                $value2 = $data2[$key];
            }
            return ['type' => '+', 'key' => $key, 'value' => $value2];
        } elseif (!array_key_exists($key, $data2)) {
            if (is_array($data1[$key])) {
                $value1 = arrayKeysInsert($data1[$key]);
            } else {
                $value1 = $data1[$key];
            }
            return ['type' => '-', 'key' => $key, 'value' => $value1];
        }
        $value1 = $data1[$key];
        $value2 = $data2[$key];
        if (is_array($value1) && is_array($value2)) {
            return ['type' => ' ', 'key' => $key, 'value' => filesDiffer($value1, $value2)];
        }
        if ($value1 === $value2) {
            return  ['type' => ' ', 'key' => $key, 'value' => $value1];
        } else {
            if (is_array($value1)) {
                $value1 = arrayKeysInsert($value1);
            }
            if (is_array($value2)) {
                $value2 = arrayKeysInsert($value2);
            }
            return ['type' => '_', 'key' => $key, 'value' => $value1, 'new_value' => $value2];
        }
    }, $sortedKeys);
     return $data;
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

function iter(mixed $key1, $value1, $level = 1)
{
    $output = array_map(function ($key, $value) use ($level) {
        if (is_array($value) && array_key_exists('value', $value)) {
            if (is_array($value['value'])) {
                $spaces = getLevelSpaces($level);
                $type = $value['type'];
                $tkey = $value['key'];
                $level++;
                $temp = iter($tkey, $value['value'], $level);
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
                $val = ($val === '') ? '' : " {$val}";
                if ($type === "_") {
                    $newVal = $value['new_value'];
                    $newVal = (is_string($newVal)) ? $newVal : json_encode($newVal);
                    $newVal = ($newVal === '') ? '' : " {$newVal}";
                    return "{$spaces}- {$tkey}:{$val}\n{$spaces}+ {$tkey}:{$newVal}\n";
                } else {
                    return "{$spaces}{$type} {$tkey}:{$val}\n";
                }
            }
        }
    }, array_keys($value1), $value1);
    return $output;
}

function getLevelSpaces(int $level)
{
    return str_repeat(' ', $level * 4 - 2);
}

function stylish(array $tree)
{
    $result[] = "{\n";
    $result[] = iter('', $tree, 1);
    $result[] = "}";
    return implode('', array_flatten($result));
}
