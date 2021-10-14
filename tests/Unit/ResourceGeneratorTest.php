<?php

namespace Unit;

use AndreaCivita\ApiCrudGenerator\Core\Generators\ResourceGenerator;
use AndreaCivita\ApiCrudGenerator\Core\Stub;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class ResourceGeneratorTest extends TestCase
{
    /**
     * @var string $modelName
     */
    private $modelName;

    /**
     * @var ResourceGenerator $resourceGenerator
     */
    private $resourceGenerator;

    public function setUp(): void
    {
        $this->modelName = 'Car';
        $this->resourceGenerator = new ResourceGenerator(new Stub(new Filesystem(), new Str()));
    }

    public function testSetData()
    {
        $this->assertInstanceOf(
            ResourceGenerator::class,
            $this->resourceGenerator->setData($this->modelName)
        );
    }


    public function testGenerate()
    {
        $this->resourceGenerator->setData($this->modelName);
        $this->assertIsInt($this->resourceGenerator->generate());
    }
}
