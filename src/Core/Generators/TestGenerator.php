<?php

namespace AndreaCivita\ApiCrudGenerator\Core\Generators;

use AndreaCivita\ApiCrudGenerator\Core\Stub;
use AndreaCivita\ApiCrudGenerator\Interfaces\Generator;
use Illuminate\Filesystem\Filesystem;

class TestGenerator implements Generator
{
    /**
     * @var string $modelName
     */
    protected $modelName;

    /**
     * @var Filesystem $fileSystem
     */
    protected $fileSystem;

    /**
     * @var Stub $stub
     */
    protected $stub;

    /**
     * @param Stub $stub
     */
    public function __construct(Stub $stub)
    {
        $this->stub = $stub;
        $this->fileSystem = $this->stub->getFilesystemInstance();
    }

    /**
     * @param string $modelName
     * @return $this
     */
    public function setData(string $modelName): TestGenerator
    {
        $this->modelName = $modelName;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
        $content = $this->stub->parseStub('Test', $this->modelName);

        if (!$this->fileSystem->exists("tests/Feature/")) {
            $this->fileSystem->makeDirectory("tests/Feature/", 0775, true);
        }
        return $this->fileSystem->append("tests/Feature/{$this->modelName}Test.php", $content);
    }
}
