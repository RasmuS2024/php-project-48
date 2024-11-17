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
        default => throw new \RuntimeException("Unknown data format: \"{$formatName}\""),
    };
}
