<?php

namespace Unit;

use AndreaCivita\ApiCrudGenerator\Core\Generators\ControllerGenerator;
use AndreaCivita\ApiCrudGenerator\Core\Generators\RequestGenerator;
use AndreaCivita\ApiCrudGenerator\Core\Stub;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class ControllerGeneratorTest extends TestCase
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
     * @var ControllerGenerator $controllerGenerator
     */
    private $controllerGenerator;

    public function setUp(): void
    {
        $this->modelName = 'Car';
        $this->tableName = 'cars';
        $this->controllerGenerator = new ControllerGenerator(new Stub(new Filesystem(), new Str()));
    }

    public function testSetData()
    {
        $this->assertInstanceOf(
            ControllerGenerator::class,
            $this->controllerGenerator->setData($this->modelName, $this->tableName)
        );
    }


    public function testGenerate()
    {
        $this->controllerGenerator->setData($this->modelName, $this->tableName);
        $this->assertIsInt($this->controllerGenerator->generate());
    }
}
