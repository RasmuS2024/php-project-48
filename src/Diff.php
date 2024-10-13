<?php

namespace GenDiff\Diff;

function verifyKeys(array $json, string $key): mixed
{
    if (isset($json[$key])) {
        $tkey = $json[$key];
        if (is_bool($tkey)) {
            $tkey = ($tkey == 1) ? 'true' : 'false';
        }
        return $tkey;
    } else {
    	return false;
    }
    
}

function getSortedKeys(array $json1, array $json2): array
{
    $keys1 = array_keys($json1);
    $keys2 = array_keys($json2);
    $keys = array_merge($keys1, $keys2);
    $keys = array_unique($keys);
    sort($keys, SORT_STRING);
    return $keys;
}


function filesDiff(string $file1Path, string $file2Path, string $parsingFormat = 'json'): string
{
    $json1 = json_decode(file_get_contents($file1Path), true);
    $json2 = json_decode(file_get_contents($file2Path), true);
    $keys = getSortedKeys($json1, $json2);
    $res = "{\n";
    foreach ($keys as $key) {
        $tkey1 = verifyKeys($json1, $key);
        $tkey2 = verifyKeys($json2, $key);
        if ($tkey1) {
            if ($tkey2) {
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
            if ($tkey2) {
            	$res = "{$res}  + {$key}: {$tkey2}\n";
            }
        }
    }
    $res = "{$res}}";
    return $res;
}
