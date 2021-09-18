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
        $content = $this->stub->parseStub('Test', $this->name);

        if (!$this->fileSystem->exists("tests/Feature/")) {
            $this->fileSystem->makeDirectory("tests/Feature/");
        }
        return $this->files->append("tests/Feature/{$this->name}Test.php", $content);
    }
}
