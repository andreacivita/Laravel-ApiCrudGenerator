<?php

namespace Unit;

use AndreaCivita\ApiCrudGenerator\Core\Generators\FactoryGenerator;
use AndreaCivita\ApiCrudGenerator\Core\Stub;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class FactoryGeneratorTest extends TestCase
{
    /**
     * @var string $modelName
     */
    private $modelName;

    /**
     * @var FactoryGenerator $factoryGenerator
     */
    private $factoryGenerator;

    public function setUp(): void
    {
        $this->modelName = 'Car';
        $this->factoryGenerator = new FactoryGenerator(new Stub(new Filesystem(), new Str()));
    }

    public function testSetData()
    {
        $this->assertInstanceOf(
            FactoryGenerator::class,
            $this->factoryGenerator->setData($this->modelName)
        );
    }


    public function testGenerate()
    {
        $this->factoryGenerator->setData($this->modelName);
        $this->assertIsInt($this->factoryGenerator->generate());
    }
}
