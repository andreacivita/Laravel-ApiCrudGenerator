<?php

namespace AndreaCivita\ApiCrudGenerator\Core\Generators;

use AndreaCivita\ApiCrudGenerator\Core\Stub;
use AndreaCivita\ApiCrudGenerator\Interfaces\Generator;
use Illuminate\Filesystem\Filesystem;

class ControllerGenerator implements Generator
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $table
     */
    protected $table;

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
     * @param string $name
     * @param string $table
     * @return ControllerGenerator
     */
    public function setData(string $name, string $table): ControllerGenerator
    {
        $this->name = $name;
        $this->table = $table;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
        $content = $this->stub->parseStub('Controller', $this->name, ['table' => $this->table]);

        if (!$this->fileSystem->exists("app/Http/Controllers/")) {
            $this->fileSystem->makeDirectory("app/Http/Controllers/", 0755, true);
        }

        return $this->fileSystem->put("app/Http/Controllers/{$this->name}Controller.php", $content);
    }
}
