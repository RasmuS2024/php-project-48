<?php

namespace Differ\Formatters;

use function Functional\flatten;
use function Differ\Formatters\Stylish\stylish;
use function Differ\Formatters\Plain\plain;
use function Differ\Formatters\Json\json;

function formatSelect(array $diffSource, string $formatName): string
{
    $result = match ($formatName) {
        'stylish' => stylish($diffSource),
        'plain' => plain($diffSource),
        'json' => json($diffSource),
        default => "Unknown format: \"{$formatName}\"",
    };
    return $result;
}

function getStringValue(mixed $value, string $format = ''): string
{
    if (is_string($value)) {
        if ($format === 'plain') {
            return "'{$value}'";
        } else {
            return $value;
        }
    }
    return json_encode($value);
}

function getStringFromArray(array $array): string
{
    $tempFlatten = flatten($array);
    return implode('', $tempFlatten);
}
