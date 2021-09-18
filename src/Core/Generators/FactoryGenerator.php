<?php

namespace AndreaCivita\ApiCrudGenerator\Core\Generators;

use AndreaCivita\ApiCrudGenerator\Core\Stub;
use AndreaCivita\ApiCrudGenerator\Interfaces\Generator;
use Illuminate\Filesystem\Filesystem;

class FactoryGenerator implements Generator
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
    public function setData(string $name): FactoryGenerator
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
        $content = $this->stub->parseStub('Factory', $this->name);

        if (!$this->fileSystem->exists("database/factories/")) {
            $this->fileSystem->makeDirectory("database/factories/");
        }
        return $this->fileSystem->put("database/factories/{$this->name}Factory.php", $content);
    }
}
