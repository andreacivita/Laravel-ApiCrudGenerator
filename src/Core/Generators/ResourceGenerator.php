<?php

namespace AndreaCivita\ApiCrudGenerator\Core\Generators;

use AndreaCivita\ApiCrudGenerator\Core\Stub;
use AndreaCivita\ApiCrudGenerator\Interfaces\Generator;
use Illuminate\Filesystem\Filesystem;

class ResourceGenerator implements Generator
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

    public function setData(string $name): ResourceGenerator
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
        $content = $this->stub->parseStub('Resource', $this->name);

        if (!$this->fileSystem->exists("app/Http/Resources/")) {
            $this->fileSystem->makeDirectory("app/Http/Resources/");
        }
        return $this->fileSystem->put("app/Http/Resources/{$this->name}Resource.php", $content);
    }
}
