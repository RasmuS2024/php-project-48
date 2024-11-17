<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseDataWithFormat(array $rawData): array
{
    return match ($rawData['extension']) {
        'json' => json_decode($rawData['data'], true),
        'yaml', 'yml' => Yaml::parse($rawData['data']),
        default => throw new \RuntimeException("Unknown data format: {$rawData['extension']}"),
    };
}
