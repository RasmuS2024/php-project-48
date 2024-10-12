<?php

namespace GenDiff\Parser;


function parsingJson(string $file1Path, string $file2Path, string $parsingFormat): string
{
	//echo "Обработка файла: {$fileJsonPath}".PHP_EOL;
	//echo "Формат файла: {$parsingFormat}".PHP_EOL;
	$json1 = json_decode(file_get_contents($file1Path), true);
	$json2 = json_decode(file_get_contents($file2Path), true);
	//var_dump(json_decode($jsonString, true));
	//var_dump($jsonString1);
	
	$keys = array_unique(array_merge(array_keys($json1), array_keys($json2)));
	//$keys = array_flatten($keys);
	sort($keys, SORT_STRING);
	//var_dump($keys);
	//var_dump($json1);
	//var_dump($json2);
	$res = "{\n";
	foreach($keys as $key) {
		if (isset($json1[$key])) {
			$tkey1 = $json1[$key];
			if (is_bool($json1[$key])) {
				$tkey1 = ($json1[$key] == 1) ? 'true' : 'false';
			}
			//var_dump($td);
			//echo PHP_EOL;
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
	$res = "{$res}}\n";
	echo $res;
	echo PHP_EOL;
	return $res;
}
