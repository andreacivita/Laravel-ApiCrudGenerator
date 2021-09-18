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

        return $this->fileSystem->put("app/Http/Controllers/{$this->name}Controller.php", $content);
    }
}
