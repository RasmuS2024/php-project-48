# Gendiff (PHP-project-48)
A library in the PHP programming language for comparing data in JSON, YAML format and displaying differences in text format. You can compare JSON and YAML files with each other.
The output of differences is implemented in 3 formats: 
 * stylish - the absence of a "+" or "-" before the key means that the key is in both files, and its values are the same. "+" means that the key has been added, "-" means that the key has been deleted if the key is repeated with a "-" sign, and then "+" means that the value of the key has changed; 
 * plain - displays each key line by line with a description of the changes (added, removed, updated). If the property value is composite, then write [complex value];
 * json - a json string containing differences is output, where the key "key" indicates the name of the key in the input data, the key "value" indicates the value of the key in the input data, the key "type" takes 4 values:
	" " - if the key value has not changed (the key is contained in both files being compared, and its value is the same);
	"-" - if the key is deleted;
 	"+" - if the key is added;
 	"\_" - if the key value has changed, the new key value is stored in the key "new_value".
The keys are displayed in alphabetical order.

Библиотека на языке программирования PHP для сравнения данных в формате JSON, YAML и вывода отличий в текстовом формате. Можно сравнить файлы JSON и YAML между собой.
Вывод отличий реализован в 3-х форматах: 
 * stylish - отсутствие "+" или "-" перед ключом значит ключ есть в обоих файлах, и его значения совпадают. "+" значит, что ключ добавлен, "-" значит, что ключ удалён, если ключ повторяется со знаком "-", а потом "+" это значит, что значение ключа изменилось; 
 * plain - выводит построчно каждый ключ с описанием изменений (added, removed, updated). Если значение свойства является составным, то пишется [complex value];
 * json - выводится строка json содержащая отличия, где ключ "key" обозначает наименование ключа во входных данных, ключ "value" обозначает значение ключа во входных данных, ключ "type" принимает 4 значения: 
 	" " - если значение ключа не изменилось (ключ содержится в обоих сравниваемых файлах, и его значение одинаково);
 	"-" - если ключ удалён;
 	"+" - если ключ добавлен;
 	"\_" - если значение ключа изменилось, при этом новое значение ключа хранится в ключе "new_value".
Ключи выводятся в алфавитном порядке.

## Hexlet tests and linter status:
[![Actions Status](https://github.com/RasmuS2024/php-project-48/actions/workflows/hexlet-check.yml/badge.svg)](https://github.com/RasmuS2024/php-project-48/actions)
## Codeclimate maintainability status:
<a href="https://codeclimate.com/github/RasmuS2024/php-project-48/maintainability"><img src="https://api.codeclimate.com/v1/badges/dd978260caa754e3367b/maintainability" /></a>
## Codeclimate Test Coverage status:
<a href="https://codeclimate.com/github/RasmuS2024/php-project-48/test_coverage"><img src="https://api.codeclimate.com/v1/badges/dd978260caa754e3367b/test_coverage" /></a>

## Prerequisites
* Linux, WSL
* PHP >= 8.1.2
* Composer
* Make
* Git

## Setup
```bash
git clone https://github.com/RasmuS2024/php-project-48.git
cd php-project-48
make install
```

## Use library
```
<?php

use function Differ\Differ\genDiff;

$format = 'plain';				// supported formats: "stylish", "plain", "json", default: "stylish"
$pathToFile1 = 'File1.json';	// paste your path to file in JSON or YAML format
$pathToFile1 = 'File2.json';	// paste your path to file in JSON or YAML format
$diff = genDiff($pathToFile1, $pathToFile2, $format);
print_r($diff);
```

### Gendiff process with json files in asciinema:
<a href="https://asciinema.org/a/sajdPQG2NqP2Ky51Kyp4SDpBp" target="_blank"><img src="https://asciinema.org/a/sajdPQG2NqP2Ky51Kyp4SDpBp.svg" width="400" height="300" /></a>
### Gendiff process with YAML files in asciinema:
<a href="https://asciinema.org/a/rV1BXXmxURj3LKG9UDLQcDJvi" target="_blank"><img src="https://asciinema.org/a/rV1BXXmxURj3LKG9UDLQcDJvi.svg" width="400" height="300" /></a>

### Gendiff process with json and YAML files with nested structure in asciinema:
<a href="https://asciinema.org/a/nUzyU01TNMRxl5NX8fDdVE54V" target="_blank"><img src="https://asciinema.org/a/nUzyU01TNMRxl5NX8fDdVE54V.svg" width="400" height="300" /></a>

### Gendiff process with select out format in asciinema:
<a href="https://asciinema.org/a/82H9oUqEatG1FHpbsW9dhUKGH" target="_blank"><img src="https://asciinema.org/a/82H9oUqEatG1FHpbsW9dhUKGH.svg" width="400" height="300" /></a>

### Gendiff process with select out format json in asciinema:
<a href="https://asciinema.org/a/McHUa58ngK5ocggDtiF20BxJ0" target="_blank"><img src="https://asciinema.org/a/McHUa58ngK5ocggDtiF20BxJ0.svg" width="400" height="300" /></a>

## Run tests
```sh
make test
```