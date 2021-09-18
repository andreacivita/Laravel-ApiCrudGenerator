<?php

namespace AndreaCivita\ApiCrudGenerator\Core\Generators;

use AndreaCivita\ApiCrudGenerator\Core\Stub;
use AndreaCivita\ApiCrudGenerator\Interfaces\Generator;
use Illuminate\Filesystem\Filesystem;

class RequestGenerator implements Generator
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
    public function setData(string $name): RequestGenerator
    {
        $this->name = $name;
        return $this;
    }
    /**
     * @inheritDoc
     */
    public function generate()
    {
        $content = $this->stub->parseStub('Request', $this->name);

        if (!$this->fileSystem->exists("app/Http/Requests/")) {
            $this->fileSystem->makeDirectory("app/Http/Requests/");
        }

        return $this->fileSystem->put("app/Http/Requests/{$this->name}Request.php", $content);
    }
}
