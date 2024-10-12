<?php

namespace GenDiff\Parser;

function parsingJson($fileJsonPath, $parsingFormat)
{
	echo "Обработка файла: {$fileJsonPath}".PHP_EOL;
	echo "Формат файла: {$parsingFormat}".PHP_EOL;
	$jsonString = file_get_contents($fileJsonPath);
	var_dump(json_decode($jsonString, true));
	echo PHP_EOL;
}
