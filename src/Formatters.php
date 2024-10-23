<?php

namespace GenDiff\Formatters;

use function GenDiff\Formatters\Stylish\stylish;
use function GenDiff\Formatters\Plain\plain;

function formatSelect(array $diffSource, string $style): string
{
    $result = match ($style) {
        'stylish' => stylish($diffSource),
        'plain' => plain($diffSource),
    };
    return $result;
}
