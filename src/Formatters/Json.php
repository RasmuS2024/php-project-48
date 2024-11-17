<?php

namespace Differ\Formatters\Json;

function getJsonFormat(array $tree): string
{
    return json_encode($tree);
}
