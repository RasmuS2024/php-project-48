<?php

namespace Differ\Formatters;

use function Functional\flatten;
use function Differ\Formatters\Stylish\getStylishFormat;
use function Differ\Formatters\Plain\getPlainFormat;
use function Differ\Formatters\Json\getJsonFormat;

function getFormattedDiff(array $diffSource, string $formatName): string
{
    return match ($formatName) {
        'stylish' => getStylishFormat($diffSource),
        'plain' => getPlainFormat($diffSource),
        'json' => getJsonFormat($diffSource),
        default => "Unknown format: \"{$formatName}\"",
    };
}

function getStringValue(mixed $value, string $format = ''): string
{
    if (is_string($value)) {
        return $format === 'plain' ? "'{$value}'" : $value;
    }
    return json_encode($value);
}

function getStringFromArray(array $array): string
{
    $tempFlatten = flatten($array);
    return implode('', $tempFlatten);
}
