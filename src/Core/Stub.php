<?php


namespace AndreaCivita\ApiCrudGenerator\Core;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

/**
 *
 * Manage Stub generation
 *
 * Class Stub
 * @package AndreaCivita\ApiCrudGenerator
 */
class Stub
{
    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * The String support instance
     *
     * @var Str
     */
    protected $str;

    /**
     * Stub constructor.
     * @param Filesystem $filesystem
     * @param Str $str
     */
    public function __construct(Filesystem $filesystem, Str $str)
    {
        $this->filesystem = $filesystem;
        $this->str = $str;
    }

    /**
     * Get the file from the stub
     *
     * @param string $name
     * @return string
     * @throws FileNotFoundException
     */
    public function getStub($name) : string
    {
        if ($this->filesystem->exists("/resources/stubs/$name.stub")) {
            return $this->filesystem->get("/resources/stubs/$name.stub");
        }

        return $this->filesystem->get(__DIR__ . "/../stubs/$name.stub");
    }


    /**
     * Fill stub with data
     *
     * @param $stub string name of stub
     * @param $name string name of resource
     * @param $args array additional placeholders to replace
     * @return string
     */
    public function parseStub(string $stub, string $name, array $args = []) : string
    {
        $toParse = array_merge([
            'modelName' => $name,
            'modelNamePluralLowerCase' => $args['table'] ?? $this->str->lower($this->str->plural($name)),
            'modelNameSingularLowerCase' => $this->str->lower($name)
        ], $args);

        try {
            return $this->str->replace(
                array_map(function ($key) {
                    return "{{{$key}}}";
                }, array_keys($toParse)),
                array_values($toParse),
                $this->getStub($stub)
            );
        } catch (FileNotFoundException $e) {
            return "Stub not found";
        }
    }


    /**
     * @return Filesystem
     */
    public function getFilesystemInstance() : Filesystem
    {
        return $this->filesystem;
    }

    /**
     * @return Str
     */
    public function getStrInstance() : Str
    {
        return $this->str;
    }
}
