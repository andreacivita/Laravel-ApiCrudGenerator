<?php

namespace AndreaCivita\ApiCrudGenerator\Core\Generators;

use AndreaCivita\ApiCrudGenerator\Core\Stub;
use AndreaCivita\ApiCrudGenerator\Interfaces\Generator;
use Illuminate\Filesystem\Filesystem;

class TestGenerator implements Generator
{
    /**
     * @var string $name
     */
    protected $name;


    /**
     * @var Filesystem $fileSystem
     */
    protected $fileSystem;

    /**
     * @var Stub $stub
     */
    protected $stub;

    /**
     * @param Filesystem $fileSystem
     * @param Stub $stub
     */
    public function __construct(Filesystem $fileSystem, Stub $stub)
    {
        $this->fileSystem = $fileSystem;
        $this->stub = $stub;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setData(string $name): TestGenerator
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
        $content = $this->stub->parseStub('Test', $this->name);

        if (!$this->fileSystem->exists("tests/Feature/")) {
            $this->fileSystem->makeDirectory("tests/Feature/");
        }
        return $this->fileSystem->append("tests/Feature/{$this->name}Test.php", $content);
    }
}
