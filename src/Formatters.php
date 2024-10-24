<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\stylish;
use function Differ\Formatters\Plain\plain;
use function Differ\Formatters\Json\json;

function formatSelect(array $diffSource, string $formatName): string
{
    $result = match ($formatName) {
        'stylish' => stylish($diffSource),
        'plain' => plain($diffSource),
        'json' => json($diffSource),
    };
    return $result;
}
