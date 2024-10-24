<?php

namespace GenDiff\Formatters\Json;

function json(array $tree): string
{
    $json_string = json_encode($tree, JSON_PRETTY_PRINT);
    return $json_string;
}
