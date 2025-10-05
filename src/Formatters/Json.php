<?php

namespace Differ\Formatters\Json;

function getJsonFormat(array $tree): mixed
{
    return json_encode($tree);
}
