<?php

namespace AndreaCivita\ApiCrudGenerator\Core\Generators;

use AndreaCivita\ApiCrudGenerator\Core\Stub;
use AndreaCivita\ApiCrudGenerator\Interfaces\Generator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ModelGenerator implements Generator
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
     * @var bool $timestamps
     */
    protected $timestamps;

    /**
     * @var Filesystem $fileSystem
     */
    protected $fileSystem;

    /**
     * @var Stub $stub
     */
    protected $stub;

    /**
     * @var Str $str
     */
    protected $str;

    /**
     * @param Stub $stub
     */
    public function __construct(Stub $stub)
    {
        $this->stub = $stub;
        $this->fileSystem = $stub->getFilesystemInstance();
        $this->str = $stub->getStrInstance();
    }

    /**
     * @param string $name
     * @param string $table
     * @param bool $timestamps
     * @return $this
     */
    public function setData(string $name, string $table, bool $timestamps): ModelGenerator
    {
        $this->name = $name;
        $this->table = $table;
        $this->timestamps = $timestamps;
        return $this;
    }


    /**
     * @inheritDoc
     */
    public function generate()
    {
        $content = $this->stub->parseStub('Model', $this->name, [
            'tableDeclaration' =>  $this->table === "default" ? $this->str->lower($this->str->plural($this->name)) : null,
            'timestamps' => $this->timestamps ? 'public $timestamps = false;' : ''
        ]);

        if (!$this->fileSystem->exists("app/Models/")) {
            $this->fileSystem->makeDirectory("app/Models/", 0755, true);
        }
        return $this->fileSystem->put("app/Models/{$this->name}.php", $content);
    }
}
