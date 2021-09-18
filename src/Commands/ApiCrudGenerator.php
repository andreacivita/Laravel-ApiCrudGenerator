<?php

namespace AndreaCivita\ApiCrudGenerator\Commands;

use AndreaCivita\ApiCrudGenerator\Core\Generator;
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
     * @param Generator $generator
     * @param Str $str
     * @param Schema $schema
     */
    public function __construct(Generator $generator, Str $str, Schema $schema)
    {
        parent::__construct();
        $this->generator = $generator;
        $this->str = $str;
        $this->schema = $schema;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() : int
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
            $tables =  DB::connection()->getDoctrineSchemaManager()->listTableNames();
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
    protected function interactive() : void
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
        $this->generator->controller($name, $table);
        $this->info("Generated Controller!");

        $this->generator->model($name, $table, $timestamps);
        $this->info("Generated Model!");

        $this->generator->request($name);
        $this->info("Generated Request!");

        $this->generator->resource($name);
        $this->info("Generated Resource!");

        $this->passport ? $this->generator->secureRoutes($name) : $this->generator->routes($name);
        $this->info("Generated routes!");

        $this->generator->factory($name);
        $this->info("Generated Factory!");

        $this->generator->test($name);
        $this->info("Generated Test!");
    }
}
