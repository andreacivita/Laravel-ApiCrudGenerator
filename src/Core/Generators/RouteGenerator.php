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
     * @param bool $secure
     * @return $this
     */
    public function setData(string $name, bool $secure): RouteGenerator
    {
        $this->name = $name;
        $this->secure = $secure;
        return $this;
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
