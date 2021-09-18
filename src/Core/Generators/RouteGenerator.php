<?php

namespace AndreaCivita\ApiCrudGenerator\Core\Generators;

use AndreaCivita\ApiCrudGenerator\Core\Stub;
use AndreaCivita\ApiCrudGenerator\Interfaces\Generator;
use Illuminate\Filesystem\Filesystem;

class RouteGenerator implements Generator
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
     * @var bool $secure
     */
    protected $secure;

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
     * @param bool $secure
     * @param Filesystem $fileSystem
     * @param Stub $stub
     */
    public function __construct(string $name, string $table, bool $secure, Filesystem $fileSystem, Stub $stub)
    {
        $this->name = $name;
        $this->table = $table;
        $this->secure = $secure;
        $this->fileSystem = $fileSystem;
        $this->stub = $stub;
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
        $fileName = $this->secure ? 'Passport-Routes' : 'Routes';
        $content = $this->stub->parseStub($fileName, $this->name);

        return $this->fileSystem->append("routes/api.php", $content);
    }
}
