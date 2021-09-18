<?php

namespace AndreaCivita\ApiCrudGenerator\Commands;

use AndreaCivita\ApiCrudGenerator\Core\Generator;
use AndreaCivita\ApiCrudGenerator\Core\Generators\ControllerGenerator;
use AndreaCivita\ApiCrudGenerator\Core\Generators\FactoryGenerator;
use AndreaCivita\ApiCrudGenerator\Core\Generators\ModelGenerator;
use AndreaCivita\ApiCrudGenerator\Core\Generators\RequestGenerator;
use AndreaCivita\ApiCrudGenerator\Core\Generators\ResourceGenerator;
use AndreaCivita\ApiCrudGenerator\Core\Generators\RouteGenerator;
use AndreaCivita\ApiCrudGenerator\Core\Generators\TestGenerator;
use Doctrine\DBAL\Driver\PDOException;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ApiCrudGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud
    {name=name : Class (singular) for example User}
    {--table=default : Table name (plural) for example users | Default is generated-plural}
    {--timestamps=false : Set default timestamps}
    {--interactive=false : Interactive mode}
    {--all=false : Interactive mode}
    {--passport=false : Secure routes with passport}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD operations';

    /**
     *
     * Generator support instance
     *
     * @var Generator
     */
    protected $generator;

    /**
     * @var ControllerGenerator $controller
     */
    protected $controller;

    /**
     * @var FactoryGenerator $factory
     */
    protected $factory;

    /**
     * @var ModelGenerator $model
     */
    protected $model;

    /**
     * @var RequestGenerator $request
     */
    protected $request;

    /**
     * @var ResourceGenerator $resource
     */
    protected $resource;

    /**
     * @var RouteGenerator $route
     */
    protected $route;


    /**
     * @var TestGenerator $test
     */
    protected $test;


    /**
     * The String support instance
     *
     * @var Str
     */
    protected $str;

    /**
     * @var bool Passport option
     */
    protected $passport;


    /**
     * Schema support instance
     *
     * @var Schema $schema
     */
    protected $schema;

    /**
     * Create a new command instance.
     *
     * @param ControllerGenerator $controllerGenerator
     * @param FactoryGenerator $factoryGenerator
     * @param ModelGenerator $modelGenerator
     * @param RequestGenerator $requestGenerator
     * @param ResourceGenerator $resourceGenerator
     * @param RouteGenerator $routeGenerator
     * @param TestGenerator $testGenerator
     * @param Str $str
     * @param Schema $schema
     */
    public function __construct(
        ControllerGenerator $controllerGenerator,
        FactoryGenerator    $factoryGenerator,
        ModelGenerator      $modelGenerator,
        RequestGenerator    $requestGenerator,
        ResourceGenerator   $resourceGenerator,
        RouteGenerator      $routeGenerator,
        TestGenerator       $testGenerator,
        Str                 $str,
        Schema              $schema
    )
    {
        parent::__construct();
        $this->controller = $controllerGenerator;
        $this->factory = $factoryGenerator;
        $this->model = $modelGenerator;
        $this->request = $requestGenerator;
        $this->resource = $resourceGenerator;
        $this->route = $routeGenerator;
        $this->test = $testGenerator;
        $this->str = $str;
        $this->schema = $schema;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        // Checking interactive mode
        if ($this->option('interactive') == "") {
            $this->interactive();
            return 0;
        }

        // Checking all mode
        if ($this->option('all') == "") {
            $this->all();
            return 0;
        }

        // Checking Passport mode
        if ($this->option('passport') == "") {
            $this->passport = true;
        }

        // If here, no interactive || all selected
        $name = ucwords($this->argument('name'));
        $table = $this->option('table');
        $timestamps = $this->option('timestamps');
        $this->generate($name, $table, $timestamps);
        return 0;
    }


    /**
     * Handle all-db generation
     */
    protected function all()
    {
        try {
            $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
            foreach ($tables as $table) {
                $this->comment("Generating " . $table . " CRUD");
                $columns = Schema::getColumnListing($table);
                $name = ucwords($this->str->singular($table));
                in_array('created_at', $columns) ? $timestamps = true : $timestamps = false;
                $this->generate($name, $table, $timestamps);
            }
        } catch (QueryException $exception) {
            $this->error("Error: " . $exception->getMessage());
        }
    }


    /**
     * Generate CRUD in interactive mode
     */
    protected function interactive(): void
    {
        $this->info("Welcome in Interactive mode");

        $this->comment("This command will guide you through creating your CRUD");
        $name = $this->ask('What is name of your Model?');
        $name = ucwords($name);
        $table = $this->ask("Table name [" . strtolower($this->str->plural($name)) . "]:");
        if ($table == "") {
            $table = $this->str->plural($name);
        }
        $table = strtolower($table);
        $choice = $this->choice('Do your table has timestamps column?', ['No', 'Yes'], 0);
        $choice === "Yes" ? $timestamps = true : $timestamps = false;
        $this->info("Please confim this data");
        $this->line("Name: $name");
        $this->line("Table: $table");
        $this->line("Timestamps:  $choice");

        $confirm = $this->ask("Press y to confirm, type N to restart");
        if ($confirm == "y") {
            $this->generate($name, $table, $timestamps);
            return;
        }
        $this->error("Aborted!");
    }


    /**
     * Handle data generation
     * @param $name string Model Name
     * @param $table string Table Name
     * @param $timestamps boolean
     */
    protected function generate(string $name, string $table, bool $timestamps)
    {
        $this->controller->setData($name, $table)->generate();
        $this->info("Generated Controller!");

        $this->model->setData($name, $table, $timestamps)->generate();
        $this->info("Generated Model!");

        $this->request->setData($name)->generate();
        $this->info("Generated Request!");

        $this->resource->setData($name)->generate();
        $this->info("Generated Resource!");

        $this->route->setData($name, $this->passport)->generate();
        $this->info("Generated routes!");

        $this->factory->setData($name)->generate();
        $this->info("Generated Factory!");

        $this->test->setData($name)->generate();
        $this->info("Generated Test!");
    }
}
