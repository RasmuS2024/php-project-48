<?php

namespace Differ\Formatters\Json;

function json(array $tree): string
{
    $json_string = json_encode($tree);
    return $json_string;
}
