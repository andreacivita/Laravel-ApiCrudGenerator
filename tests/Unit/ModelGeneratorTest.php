<?php

namespace Unit;

use AndreaCivita\ApiCrudGenerator\Core\Generators\ModelGenerator;
use AndreaCivita\ApiCrudGenerator\Core\Stub;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class ModelGeneratorTest extends TestCase
{
    /**
     * @var string $modelName
     */
    private $modelName;


    /**
     * @var string $tableName
     */
    private $tableName;


    /**
     * @var ModelGenerator $modelGenerator
     */
    private $modelGenerator;

    public function setUp(): void
    {
        $this->modelName = 'Car';
        $this->tableName = 'cars';
        $this->modelGenerator = new ModelGenerator(new Stub(new Filesystem(), new Str()));
    }

    public function testSetData()
    {
        $this->assertInstanceOf(
            ModelGenerator::class,
            $this->modelGenerator->setData($this->modelName, $this->tableName, false)
        );
    }


    public function testGenerate()
    {
        $this->modelGenerator->setData($this->modelName, $this->tableName, true);
        $this->assertIsInt($this->modelGenerator->generate());
    }
}
