<?php

use PHPUnit\Framework\TestCase;
use AndreaCivita\ApiCrudGenerator\Core\Generator;

class GeneratorTest extends TestCase
{
    protected $name;
    protected $table;
    protected $generator;
    protected $files;

    /**
     * GeneratorTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->files = new Illuminate\Filesystem\Filesystem();
    }


    public function setUp(): void
    {
        parent::setUp();
        $this->name = "Car";
        $this->table = "cars";
        $this->generator = new Generator(
            new Illuminate\Filesystem\Filesystem(),
            new \Illuminate\Support\Str(),
            new \AndreaCivita\ApiCrudGenerator\Core\Stub(
                new Illuminate\Filesystem\Filesystem(),
                new \Illuminate\Support\Str()
            )
        );
    }

    public function testModel() : void
    {
        $this->files->makeDirectory('app/Models', 0777, true);
        $this->assertIsInt($this->generator->model($this->name, $this->table, true));
        $this->assertIsInt($this->generator->model($this->name, $this->table, false));
    }

    public function testController() : void
    {
        $this->files->makeDirectory('app/Http/Controllers', 0777, true);
        $this->assertIsInt($this->generator->controller($this->name, $this->table));
    }

    public function testRequest() : void
    {
        $this->files->makeDirectory('app/Http/Requests', 0777, true);
        $this->assertIsInt($this->generator->request($this->name));
    }

    public function testResource() : void
    {
        $this->files->makeDirectory('app/Http/Resources', 0777, true);
        $this->assertIsInt($this->generator->resource($this->name));
    }

    public function testFactory() : void
    {
        $this->files->makeDirectory('database/factories', 0777, true);
        $this->assertIsInt($this->generator->factory($this->name));
    }

    public function testRoutes() : void
    {
        $this->files->makeDirectory('routes', 0777, true);
        $this->files->put('routes/api.php', "");
        $this->assertIsInt($this->generator->routes($this->name));
    }

    public function testSecureRoutes() : void
    {
        $this->files->makeDirectory('routes', 0777, true);
        $this->files->put('routes/api.php', "");
        $this->assertIsInt($this->generator->secureRoutes($this->name));
    }

    public function testMakeTests(): void
    {
        $this->files->makeDirectory('tests/Feature', 0777, true);
        $this->assertIsInt($this->generator->test($this->name));
    }


    public function tearDown(): void
    {
        $files = new Illuminate\Filesystem\Filesystem();
        $files->cleanDirectory('app');
        $files->cleanDirectory('database');
        $files->cleanDirectory('routes');
        $files->cleanDirectory('tests/Feature');
        $files->deleteDirectory('app');
        $files->deleteDirectory('database');
        $files->deleteDirectory('routes');
        $files->deleteDirectory('tests/Feature');
    }
}
