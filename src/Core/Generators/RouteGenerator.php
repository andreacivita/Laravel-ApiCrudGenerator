<?php

namespace AndreaCivita\ApiCrudGenerator\Core\Generators;

use AndreaCivita\ApiCrudGenerator\Core\Stub;
use AndreaCivita\ApiCrudGenerator\Interfaces\Generator;
use Illuminate\Filesystem\Filesystem;

class RouteGenerator implements Generator
{
    /**
     * @var string $modelName
     */
    protected $modelName;

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
     *
     * @param Stub $stub
     */
    public function __construct(Stub $stub)
    {
        $this->stub = $stub;
        $this->fileSystem = $this->stub->getFilesystemInstance();
    }

    /**
     * @param string $modelName
     * @param bool $secure
     * @return $this
     */
    public function setData(string $modelName, bool $secure): RouteGenerator
    {
        $this->modelName = $modelName;
        $this->secure = $secure;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function generate()
    {
        $fileName = $this->secure ? 'Passport-Routes' : 'Routes';
        $content = $this->stub->parseStub($fileName, $this->modelName);

        if (!$this->fileSystem->exists("routes/")) {
            $this->fileSystem->makeDirectory("routes/", 0755, true);
            $this->fileSystem->put('routes/api.php', "");
        }

        return $this->fileSystem->append("routes/api.php", $content);
    }
}
