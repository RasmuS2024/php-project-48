<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseDataWithFormat(string $data, string $format): mixed
{
    $result = match ($format) {
        'json' => json_decode($data, true),
        'yaml', 'yml' => Yaml::parse($data),
        default => null,
    };
    return $result ?? false;
}
