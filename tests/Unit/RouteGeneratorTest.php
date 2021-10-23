<?php

namespace Unit;

use AndreaCivita\ApiCrudGenerator\Core\Generators\RouteGenerator;
use AndreaCivita\ApiCrudGenerator\Core\Stub;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class RouteGeneratorTest extends TestCase
{
    /**
     * @var string $modelName
     */
    private $modelName;

    /**
     * @var RouteGenerator $routeGenerator
     */
    private $routeGenerator;

    public function setUp(): void
    {
        $this->modelName = 'Car';
        $this->routeGenerator = new RouteGenerator(new Stub(new Filesystem(), new Str()));
    }

    public function testSetData()
    {
        $this->assertInstanceOf(
            RouteGenerator::class,
            $this->routeGenerator->setData($this->modelName, false)
        );
    }


    public function testGenerate()
    {
        $this->routeGenerator->setData($this->modelName, false);
        $this->assertIsInt($this->routeGenerator->generate());
    }
}
