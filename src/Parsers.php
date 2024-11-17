<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseDataWithFormat(string $data, string $format): array
{
    $result = match ($format) {
        'json' => json_decode($data, true),
        'yaml', 'yml' => Yaml::parse($data),
        default => false,
    };
    return ($result != false) ? ['success' => true, 'data' => $result] : ['success' => false];
}
