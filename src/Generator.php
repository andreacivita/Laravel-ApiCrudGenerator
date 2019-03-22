<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 22/03/2019
 * Time: 11:16
 */

namespace AndreaCivita\ApiCrudGenerator;


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
     * @var \AndreaCivita\ApiCrudGenerator\Stub;
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

        if (!$this->files->exists(app_path("Models/"))) {
            $this->files->makeDirectory(app_path("Models/"));
        }
        $this->files->put(app_path("Models/{$name}.php"), $content);
    }

    /**
     * Create controller from controller.stub
     *
     * @param $name
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function controller($name)
    {
        $content = $this->stub->parseStub('Controller', $name);

        $this->files->put(app_path("Http/Controllers/{$name}Controller.php"), $content);
    }

    /**
     * Generate Request from request.stub
     *
     * @param $name
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function request($name)
    {
        $content = $this->stub->parseStub('Request', $name);

        if (!$this->files->exists(app_path("Http/Requests/"))) {
            $this->files->makeDirectory(app_path("Http/Requests/"));
        }
        $this->files->put(app_path("Http/Requests/{$name}Request.php"), $content);
    }

    /**
     * Generate Resource from Resource.stub
     *
     * @param $name
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function resource($name)
    {
        $content = $this->stub->parseStub('Resource', $name);

        if (!$this->files->exists(app_path("Http/Resources/"))) {
            $this->files->makeDirectory(app_path("Http/Resources/"));
        }
        $this->files->put(app_path("Http/Resources/{$name}Resource.php"), $content);
    }

    /**
     * Generate factory from Factory.stub
     *
     * @param $name
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function factory($name)
    {
        $content = $this->stub->parseStub('Factory', $name);

        if (!$this->files->exists(base_path("database/factories/"))) {
            $this->files->makeDirectory(base_path("database/factories/"));
        }
        $this->files->put(base_path("database/factories/{$name}Factory.php"), $content);
    }

    /**
     * Generate routes
     *
     * @param $name
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function routes($name)
    {
        $content = $this->stub->parseStub('Routes', $name);

        $this->files->append(base_path("routes/api.php"), $content);
    }

    /**
     * Generate unit test
     *
     * @param $name
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function test($name)
    {
        $content = $this->stub->parseStub('Test', $name);

        if (!$this->files->exists(base_path("tests/Feature/"))) {
            $this->files->makeDirectory(base_path("tests/Feature/"));
        }
        $this->files->append(base_path("tests/Feature/{$name}Test.php"), $content);
    }
}