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
    protected $files;

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
        $this->files = $filesystem;
        $this->str = $str;
    }

    /**
     * Get the file from the stub
     *
     * @param $type
     * @return string
     * @throws FileNotFoundException
     */
    protected function getStub($type) : string
    {
        if ($this->files->exists("/resources/stubs/$type.stub")) {
            return $this->files->get("/resources/stubs/$type.stub");
        }

        return $this->files->get(__DIR__ . "/../stubs/{$type}.stub");
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
}
