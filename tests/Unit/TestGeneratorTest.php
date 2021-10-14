<?php

namespace Unit;

use AndreaCivita\ApiCrudGenerator\Core\Generators\TestGenerator;
use AndreaCivita\ApiCrudGenerator\Core\Stub;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class TestGeneratorTest extends TestCase
{
    /**
     * @var string $modelName
     */
    private $modelName;

    /**
     * @var TestGenerator $testGenerator
     */
    private $testGenerator;

    public function setUp(): void
    {
        $this->modelName = 'Car';
        $this->testGenerator = new TestGenerator(new Stub(new Filesystem(), new Str()));
    }

    public function testSetData()
    {
        $this->assertInstanceOf(
            TestGenerator::class,
            $this->testGenerator->setData($this->modelName)
        );
    }


    public function testGenerate()
    {
        $this->testGenerator->setData($this->modelName);
        $this->assertIsInt($this->testGenerator->generate());
    }
}
