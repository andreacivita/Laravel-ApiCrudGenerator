<?php

use PHPUnit\Framework\TestCase;
use AndreaCivita\ApiCrudGenerator\Core\Generator;

class GeneratorTest extends TestCase
{

    protected $name;
    protected $generator;
    protected $files;

    public function setUp(): void
    {

        parent::setUp();
        $this->name = "Car";
        $this->generator = new Generator(
            new Illuminate\Filesystem\Filesystem(),
            new \Illuminate\Support\Str(),
            new \AndreaCivita\ApiCrudGenerator\Core\Stub(
                new Illuminate\Filesystem\Filesystem(),
                new \Illuminate\Support\Str()
            )
        );
    }

    public function testModel()
    {
        mkdir('app/Models',0777, true);
        $this->assertIsInt($this->generator->model($this->name,"cars",true));
    }

    public function testController()
    {
        mkdir('app/Http/Controllers',0777, true);
        $this->assertIsInt($this->generator->controller($this->name));
    }

    public function testRequest()
    {
        mkdir('app/Http/Requests',0777, true);
        $this->assertIsInt($this->generator->request($this->name));
    }

    public function testResource()
    {
        mkdir('app/Http/Resources',0777, true);
        $this->assertIsInt($this->generator->resource($this->name));
    }

    public function testFactory()
    {
        mkdir('database/factories',0777, true);
        $this->assertIsInt($this->generator->factory($this->name));
    }

    public function testRoutes()
    {
        mkdir('routes',0777, true);
        touch('routes/api.php',0777, true);
        $this->assertIsInt($this->generator->routes($this->name));
    }

    public function testMakeTests()
    {
        mkdir('tests/Feature',0777, true);
        $this->assertIsInt($this->generator->test($this->name));
    }


    public function tearDown()
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