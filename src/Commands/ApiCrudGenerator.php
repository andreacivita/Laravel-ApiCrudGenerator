<?php

namespace AndreaCivita\ApiCrudGenerator\Commands;

use AndreaCivita\ApiCrudGenerator\Core\Generator;
use Illuminate\Console\Command;
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
     * @var \AndreaCivita\ApiCrudGenerator\Core\Generator
     */
    protected $generator;


    /**
     * The String support instance
     *
     * @var \Illuminate\Support\Str
     */
    protected $str;

    /**
     * @var bool Passport option
     */
    protected $passport;

    /**
     * Db support istance
     *
     * @var \Illuminate\Support\Facades\DB $db
     */
    protected $db;

    /**
     * Schema support instance
     *
     * @var \Illuminate\Support\Facades\Schema $schema
     */
    protected $schema;

    /**
     * Create a new command instance.
     *
     * @param Generator $generator
     * @param Str $str
     * @param DB $db
     * @param Schema $schema
     */
    public function __construct(Generator $generator, Str $str, DB $db, Schema $schema)
    {
        parent::__construct();
        $this->generator = $generator;
        $this->str = $str;
        $this->db = $db;
        $this->schema = $schema;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
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
        if ($this->option('passport') == "")
            $this->passport = true;

        // If here, no interactive || all selected
        $name = ucwords($this->argument('name'));
        $table = $this->option('table');
        $timestamps = $this->option('timestamps');
        $this->generate($name, $table, $timestamps);
        return 0;
    }


    /**
     * Generate CRUD in interactive mode
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function interactive()
    {
        $this->info("Welcome in Interactive mode");

        $this->comment("This command will guide you through creating your CRUD");
        $name = $this->ask('What is name of your Model?');
        $name = ucwords($name);
        $table = $this->ask("Table name [" . strtolower($this->str->plural($name)) . "]:");
        if ($table == "")
            $table = $this->str->plural($name);
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
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function generate($name, $table, $timestamps)
    {
        $this->generator->controller($name);
        $this->info("Generated Controller!");
        $this->generator->model($name, $table, $timestamps);
        $this->info("Generated Model!");
        $this->generator->request($name);
        $this->info("Generated Request!");
        $this->generator->resource($name);
        $this->info("Generated Resource!");
        if ($this->passport)
            $this->generator->secureRoutes($name);
        else
            $this->generator->routes($name);
        $this->info("Generated routes!");
        $this->generator->factory($name);
        $this->info("Generated Factory!");
        $this->generator->test($name);
        $this->info("Generated Test!");
    }


    /**
     * Handle all-db generation
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function all()
    {
        try {
            $tables =  DB::select('SHOW TABLES');
            foreach ($tables as $table) {
                $this->comment("Generating " . $table->Tables_in_crud . " CRUD");
                $columns = $this->schema->getColumnListing($table->Tables_in_crud);
                $table = $table->Tables_in_crud;
                $name = ucwords($this->str->singular($table));
                in_array('created_at', $columns) ? $timestamps = true : $timestamps = false;
                $this->generate($name, $table, $timestamps);
            }
        } catch (QueryException $exception) {
            $this->error("Error: " . $exception->getMessage());
        }
    }
}
