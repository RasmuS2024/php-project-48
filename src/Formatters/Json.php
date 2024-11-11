<?php

namespace Differ\Formatters\Json;

function getJsonFormat(array $tree): string
{
    $json_string = json_encode($tree);
    return $json_string;
}
