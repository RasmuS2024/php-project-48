<?php

namespace GenDiff\Formatters;

use function GenDiff\Formatters\Stylish\stylish;
use function GenDiff\Formatters\Plain\plain;
use function GenDiff\Formatters\Json\json;

function formatSelect(array $diffSource, string $formatName): string
{
    $result = match ($formatName) {
        'stylish' => stylish($diffSource),
        'plain' => plain($diffSource),
        'json' => json($diffSource),
    };
    return $result;
}
