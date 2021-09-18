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
     * @param string $name
     * @param string $table
     * @param Filesystem $fileSystem
     * @param Stub $stub
     */
    public function __construct(string $name, string $table, Filesystem $fileSystem, Stub $stub)
    {
        $this->name = $name;
        $this->table = $table;
        $this->fileSystem = $fileSystem;
        $this->stub = $stub;
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
