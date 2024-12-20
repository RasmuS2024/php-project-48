<?php

namespace GenDiff\Parsers;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;
use function Differ\Parsers\parseDataWithFormat;

class DiffTest extends TestCase
{
    public function getFixtureFullPath(string $fixtureName): mixed
    {
        if (strlen($fixtureName) === 0) {
            return '';
        }
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function testFilesDiffJson(): void
    {
        $pathToFile1 = $this->getFixtureFullPath('File1.json');
        $pathToFile2 = $this->getFixtureFullPath('File2.json');
        $pathToFile3 = $this->getFixtureFullPath('Result.txt');
        $diffStringFromFiles = genDiff($pathToFile1, $pathToFile2);
        $this->assertStringEqualsFile($pathToFile3, $diffStringFromFiles);
    }

    public function testFilesDiffYaml(): void
    {
        $pathToFile1 = $this->getFixtureFullPath('File1.yaml');
        $pathToFile2 = $this->getFixtureFullPath('File2.yml');
        $pathToFile3 = $this->getFixtureFullPath('Result.txt');
        $diffStringFromFiles = genDiff($pathToFile1, $pathToFile2);
        $this->assertStringEqualsFile($pathToFile3, $diffStringFromFiles);
    }

    public function testFilesDiffYamlWithJson(): void
    {
        $pathToFile1 = $this->getFixtureFullPath('File1.yaml');
        $pathToFile2 = $this->getFixtureFullPath('File2.json');
        $pathToFile3 = $this->getFixtureFullPath('Result.txt');
        $diffStringFromFiles = genDiff($pathToFile1, $pathToFile2);
        $this->assertStringEqualsFile($pathToFile3, $diffStringFromFiles);
    }

    public function testFilesDiffJsonToPlainFormat(): void
    {
        $pathToFile1 = $this->getFixtureFullPath('File1.json');
        $pathToFile2 = $this->getFixtureFullPath('File2.json');
        $pathToFile3 = $this->getFixtureFullPath('Plainresult.txt');
        $diffStringFromFiles = genDiff($pathToFile1, $pathToFile2, 'plain');
        $this->assertStringEqualsFile($pathToFile3, $diffStringFromFiles);
    }

    public function testFilesDiffJsonToJsonFormat(): void
    {
        $pathToFile1 = $this->getFixtureFullPath('File1.json');
        $pathToFile2 = $this->getFixtureFullPath('File2.json');
        $pathToFile3 = $this->getFixtureFullPath('Jsonresult.txt');
        $diffStringFromFiles = genDiff($pathToFile1, $pathToFile2, 'json');
        $this->assertStringEqualsFile($pathToFile3, $diffStringFromFiles);
    }

    public function testFileNotAllowedExtension(): void
    {
        $this->expectException(\RuntimeException::class);
        $pathToFile1 = $this->getFixtureFullPath('File1.jso');
        $pathToFile2 = $this->getFixtureFullPath('File2.json');
        $diffStringFromFiles = genDiff($pathToFile1, $pathToFile2);
    }

    public function testFileNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $pathToFile1 = $this->getFixtureFullPath('File1.json');
        $pathToFile2 = $this->getFixtureFullPath('File5.json');
        $diffStringFromFiles = genDiff($pathToFile1, $pathToFile2);
    }

    public function testParseDataWithFormatFunction()
    {
        $this->expectException(\RuntimeException::class);
        $result = parseDataWithFormat(['data' => '{"key": "value"}', 'extension' => 'txt']);
    }
}
