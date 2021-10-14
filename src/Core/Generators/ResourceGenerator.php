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
     * @param Stub $stub
     */
    public function __construct(Stub $stub)
    {
        $this->stub = $stub;
        $this->fileSystem = $this->stub->getFilesystemInstance();
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
            $this->fileSystem->makeDirectory("app/Http/Resources/", 0755, true);
        }
        return $this->fileSystem->put("app/Http/Resources/{$this->name}Resource.php", $content);
    }
}
