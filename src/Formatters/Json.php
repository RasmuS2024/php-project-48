<?php

namespace Differ\Formatters\Json;

function json(array $tree): string
{
    //$json_string = json_encode($tree, JSON_PRETTY_PRINT);
    $json_string = json_encode($tree);
    return $json_string;
}
