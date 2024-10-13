<?php

namespace GenDiff\Diff;

function filesDiff(string $file1Path, string $file2Path, string $parsingFormat = 'json'): string
{
    $json1 = json_decode(file_get_contents($file1Path), true);
    $json2 = json_decode(file_get_contents($file2Path), true);
    $keys = array_unique(array_merge(array_keys($json1), array_keys($json2)));
    sort($keys, SORT_STRING);
    $res = "{\n";
    foreach ($keys as $key) {
        if (isset($json1[$key])) {
            $tkey1 = $json1[$key];
            if (is_bool($json1[$key])) {
                $tkey1 = ($json1[$key] == 1) ? 'true' : 'false';
            }
            if (isset($json2[$key])) {
                $tkey2 = $json2[$key];
                if (is_bool($json2[$key])) {
                    $tkey2 = ($json2[$key] == 1) ? 'true' : 'false';
                }
                if ($tkey1 === $tkey2) {
                    $res = "{$res}    {$key}: {$tkey1}\n";
                } else {
                    $res = "{$res}  - {$key}: {$tkey1}\n";
                    $res = "{$res}  + {$key}: {$tkey2}\n";
                }
            } else {
                $res = "{$res}  - {$key}: {$tkey1}\n";
            }
        } else {
            if (isset($json2[$key])) {
                $tkey2 = $json2[$key];
                if (is_bool($json2[$key])) {
                    $tkey2 = ($json2[$key] == 1) ? 'true' : 'false';
                }
            }
            $res = "{$res}  + {$key}: {$tkey2}\n";
        }
    }
    $res = "{$res}}";
    return $res;
}
