<?php

namespace Unit;

use AndreaCivita\ApiCrudGenerator\Core\Generators\RequestGenerator;
use AndreaCivita\ApiCrudGenerator\Core\Stub;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class RequestGeneratorTest extends TestCase
{
    /**
     * @var string $modelName
     */
    private $modelName;

    /**
     * @var RequestGenerator $requestGenerator
     */
    private $requestGenerator;

    public function setUp(): void
    {
        $this->modelName = 'Car';
        $this->requestGenerator = new RequestGenerator(new Stub(new Filesystem(), new Str()));
    }

    public function testSetData()
    {
        $this->assertInstanceOf(
            RequestGenerator::class,
            $this->requestGenerator->setData($this->modelName)
        );
    }


    public function testGenerate()
    {
        $this->requestGenerator->setData($this->modelName);
        $this->assertIsInt($this->requestGenerator->generate());
    }
}
