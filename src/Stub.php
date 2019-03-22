<?php


namespace AndreaCivita\ApiCrudGenerator;

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
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The String support instance
     *
     * @var \Illuminate\Support\Str
     */
    protected $str;

    /**
     * Stub constructor.
     * @param Filesystem $filesystem
     * @param Str $str
     */
    public function __construct(Filesystem $filesystem,Str $str)
    {
        $this->files = $filesystem;
        $this->str = $str;
    }


    /**
     * Get the file from the stub
     *
     * @param $type
     * @return bool|string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function getStub($type)
    {
        if ($this->files->exists(resource_path("stubs/$type.stub"))) {
            return $this->files->get(resource_path("stubs/$type.stub"));
        }

        return $this->files->get(__DIR__ . "/../stubs/{$type}.stub");
    }

    /**
     * Fill stub with data
     *
     * @param $stub string name of stub
     * @param $name string name of resource
     * @param $args array additional placeholders to replace
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function parseStub($stub, $name, $args = [])
    {
        $toParse = array_merge([
            'modelName' => $name,
            'modelNamePluralLowerCase' => strtolower($this->str->plural($name)),
            'modelNameSingularLowerCase' => strtolower($name)
        ], $args);

        return str_replace(
            array_map(function ($key) {
                return "{{{$key}}}";
            }, array_keys($toParse)),
            array_values($toParse),
            $this->getStub($stub)
        );
    }

}