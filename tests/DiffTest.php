<?php

namespace GenDiff\Parsers;

use PHPUnit\Framework\TestCase;

use function GenDiff\Parsers\genDiff;

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
        $pathToFile1 = $this->getFixtureFullPath('file1.json');
        $pathToFile2 = $this->getFixtureFullPath('file2.json');
        $pathToFile3 = $this->getFixtureFullPath('file3.txt');
        $diffStringFromFiles = genDiff($pathToFile1, $pathToFile2);
        $this->assertStringEqualsFile($pathToFile3, $diffStringFromFiles);
    }

    public function testFilesDiffJson2(): void
    {
        $pathToFile1 = $this->getFixtureFullPath('2/file1.json');
        $pathToFile2 = $this->getFixtureFullPath('2/file2.json');
        $pathToFile3 = $this->getFixtureFullPath('2/result.txt');
        $diffStringFromFiles = genDiff($pathToFile1, $pathToFile2);
        $this->assertStringEqualsFile($pathToFile3, $diffStringFromFiles);
    }

    public function testFilesDiffYaml(): void
    {
        $pathToFile1 = $this->getFixtureFullPath('file1.yaml');
        $pathToFile2 = $this->getFixtureFullPath('file2.yml');
        $pathToFile3 = $this->getFixtureFullPath('yamlres.txt');
        $diffStringFromFiles = genDiff($pathToFile1, $pathToFile2);
        $this->assertStringEqualsFile($pathToFile3, $diffStringFromFiles);
    }

    public function testFilesDiffYaml2(): void
    {
        $pathToFile1 = $this->getFixtureFullPath('2/file1.yaml');
        $pathToFile2 = $this->getFixtureFullPath('2/file2.yml');
        $pathToFile3 = $this->getFixtureFullPath('2/result.txt');
        $diffStringFromFiles = genDiff($pathToFile1, $pathToFile2);
        $this->assertStringEqualsFile($pathToFile3, $diffStringFromFiles);
    }

    public function testFilesDiffJsonToPlainFormat(): void
    {
        $pathToFile1 = $this->getFixtureFullPath('2/file1.json');
        $pathToFile2 = $this->getFixtureFullPath('2/file2.json');
        $pathToFile3 = $this->getFixtureFullPath('2/Plainresult.txt');
        $diffStringFromFiles = genDiff($pathToFile1, $pathToFile2, 'plain');
        $this->assertStringEqualsFile($pathToFile3, $diffStringFromFiles);
    }
}
