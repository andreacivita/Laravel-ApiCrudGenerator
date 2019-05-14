<?php

namespace AndreaCivita\ApiCrudGenerator\Core;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class Generator
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
     * The Stub support instance
     * 
     * @var \AndreaCivita\ApiCrudGenerator\Core\Stub;
     */
    protected $stub;

    public function __construct(Filesystem $files, Str $str, Stub $stub)
    {
        $this->files = $files;
        $this->str = $str;
        $this->stub = $stub;
    }

    /**
     * Generate model class from stubs
     *
     * @param $name string name of model class
     * @param $table string name of DB table
     * @param $timestamps boolean set timestamps true | false
     * @return bool|int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function model($name, $table, $timestamps)
    {
        $table === "default" ? $table = strtolower($this->str->plural($name)) : null;

        $timeDeclaration = "";
        if ($timestamps === false) {
            $timeDeclaration = 'public $timestamps = false;';
        }

        $content = $this->stub->parseStub('Model', $name, [
            'tableDeclaration' => $table,
            'timestamps' => $timeDeclaration
        ]);

        if (!$this->files->exists("app/Models/")) {
            $this->files->makeDirectory("app/Models/");
        }
        return $this->files->put("app/Models/{$name}.php", $content);
    }

    /**
     * Create controller from controller.stub
     *
     * @param $name string name of model class
     * @return bool|int
     */
    public function controller($name)
    {
        $content = $this->stub->parseStub('Controller', $name);

        return $this->files->put("app/Http/Controllers/{$name}Controller.php", $content);
    }

    /**
     * Generate Request from request.stub
     *
     * @param $name
     * @return bool|int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function request($name)
    {
        $content = $this->stub->parseStub('Request', $name);

        if (!$this->files->exists("app/Http/Requests/")) {
            $this->files->makeDirectory("app/Http/Requests/");
        }
        return $this->files->put("app/Http/Requests/{$name}Request.php", $content);
    }

    /**
     * Generate Resource from Resource.stub
     *
     * @param $name
     * @return bool|int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function resource($name)
    {
        $content = $this->stub->parseStub('Resource', $name);

        if (!$this->files->exists("app/Http/Resources/")) {
            $this->files->makeDirectory("app/Http/Resources/");
        }
        return $this->files->put("app/Http/Resources/{$name}Resource.php", $content);
    }

    /**
     * Generate factory from Factory.stub
     *
     * @param $name
     * @return int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function factory($name)
    {
        $content = $this->stub->parseStub('Factory', $name);

        if (!$this->files->exists("database/factories/")) {
            $this->files->makeDirectory("database/factories/");
        }
        return $this->files->put("database/factories/{$name}Factory.php", $content);
    }

    /**
     * Generate routes
     *
     * @param $name
     * @return int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function routes($name)
    {
        $content = $this->stub->parseStub('Routes', $name);

        return $this->files->append("routes/api.php", $content);
    }

    /**
     * @param $name
     * @return int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function secureRoutes($name)
    {
        $content = $this->stub->parseStub('Passport-Routes', $name);

        return $this->files->append("routes/api.php", $content);
    }

    /**
     * Generate unit test
     *
     * @param $name
     * @return int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function test($name)
    {
        $content = $this->stub->parseStub('Test', $name);

        if (!$this->files->exists("tests/Feature/")) {
            $this->files->makeDirectory("tests/Feature/");
        }
        return $this->files->append("tests/Feature/{$name}Test.php", $content);
    }
}