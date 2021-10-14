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
     * @param Stub $stub
     */
    public function __construct(Stub $stub)
    {
        $this->stub = $stub;
        $this->fileSystem = $stub->getFilesystemInstance();
    }

    /**
     * @param string $name
     * @return RequestGenerator $this
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
            $this->fileSystem->makeDirectory("app/Http/Requests/", 0755, true);
        }

        return $this->fileSystem->put("app/Http/Requests/{$this->name}Request.php", $content);
    }
}
