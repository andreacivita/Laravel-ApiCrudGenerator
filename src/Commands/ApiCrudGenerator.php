<?php

namespace AndreaCivita\ApiCrudGenerator\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

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
    {--all=false : Interactive mode}';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD operations';

    /**
     * Create a new command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Checking interactive mode
        if ($this->option('interactive') == "") {
            $this->interactive();
            return 0;
        }

        // Checkig all mode
        if ($this->option('all') == "") {
            $this->all();
            return 0;
        }

        // If here, no interactive || all selected
        $name = ucwords($this->argument('name'));
        $table = $this->option('table');
        $timestamps = $this->option('timestamps');
        $this->generate($name, $table, $timestamps);
        return 0;
    }

    /**
     * Get the file from the stub
     *
     * @param $type
     * @return bool|string
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
     * @return void
     */
    protected function parseStub($stub, $name, $args = []) 
    {
        $toParse = array_merge([
            'modelName' => $name,
            'modelNamePluralLowerCase' => strtolower(str_plural($name)),
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

    /**
     * Generate model class from stubs
     *
     * @param $name string name of model class
     * @param $table string name of DB table
     * @param $timestamps boolean set timestamps true | false
     */
    protected function model($name, $table, $timestamps)
    {
        $table === "default" ? $table = strtolower(str_plural($name)) : null;

        $timeDeclaration = "";
        if ($timestamps == "false") {
            $timeDeclaration = 'public $timestamps = false;';
        }

        $content = $this->parseStub('Model', $name, [
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
     */
    protected function controller($name)
    {
        $content = $this->parseStub('Controller', $name);

        $this->files->put(app_path("Http/Controllers/{$name}Controller.php"), $content);
    }

    /**
     * Generate Request from request.stub
     *
     * @param $name
     */
    protected function request($name)
    {
        $content = $this->parseStub('Request', $name);

        if (!$this->files->exists(app_path("Http/Requests/"))) {
            $this->files->makeDirectory(app_path("Http/Requests/"));
        }
        $this->files->put(app_path("Http/Requests/{$name}Request.php"), $content);
    }

    /**
     * Generate Resource from Resource.stub
     *
     * @param $name
     */
    protected function resource($name)
    {
        $content = $this->parseStub('Resource', $name);

        if (!$this->files->exists(app_path("Http/Resources/"))) {
            $this->files->makeDirectory(app_path("Http/Resources/"));
        }
        $this->files->put(app_path("Http/Resources/{$name}Resource.php"), $content);
    }

    /**
     * Generate factory from Factory.stub
     *
     * @param $name
     */
    protected function factory($name)
    {
        $content = $this->parseStub('Factory', $name);

        if (!$this->files->exists(base_path("database/factories/"))) {
            $this->files->makeDirectory(base_path("database/factories/"));
        }
        $this->files->put(base_path("database/factories/{$name}Factory.php"), $content);
    }

    /**
     * Generate routes
     *
     * @param $name
     */
    protected function routes($name)
    {
        $content = $this->parseStub('Routes', $name);

        $this->files->append(base_path("routes/api.php"), $content);
    }

    /**
     * Generate unit test
     *
     * @param $name
     */
    protected function test($name)
    {
        $content = $this->parseStub('Test', $name);

        if (!$this->files->exists(base_path("tests/Feature/"))) {
            $this->files->makeDirectory(base_path("tests/Feature/"));
        }
        $this->files->append(base_path("tests/Feature/{$name}Test.php"), $content);
    }

    /**
     * Generate CRUD in interactive mode
     */
    protected function interactive()
    {
        $this->info("Welcome in Interactive mode");

        $this->comment("This command will guide you through creating your CRUD");
        $name = $this->ask('What is name of your Model?');
        $name = ucwords($name);
        $table = $this->ask("Table name [" . strtolower(str_plural($name)) . "]:");
        if ($table == "")
            $table = str_plural($name);
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
            die;
        }
        $this->error("Aborted!");


    }


    /**
     * Handle data generation
     * @param $name string Model Name
     * @param $table string Table Name
     * @param $timestamps boolean
     */
    protected function generate($name, $table, $timestamps)
    {
        $this->controller($name);
        $this->info("Generated Controller!");
        $this->model($name, $table, $timestamps);
        $this->info("Generated Model!");
        $this->request($name);
        $this->info("Generated Request!");
        $this->resource($name);
        $this->info("Generated Resource!");
        $this->routes($name);
        $this->info("Generated routes!");
        $this->factory($name);
        $this->info("Generated Factory!");
        $this->test($name);
        $this->info("Generated Test!");
    }


    /**
     * Handle all-db generation
     */
    protected function all()
    {
        try {
            $tables = DB::select('SHOW TABLES');
            foreach ($tables as $table) {
                $this->comment("Generating " . $table->Tables_in_crud . " CRUD");
                $columns = Schema::getColumnListing($table->Tables_in_crud);
                $table = $table->Tables_in_crud;
                $name = ucwords(str_singular($table));
                in_array('created_at', $columns) ? $timestamps = true : $timestamps = false;
                $this->generate($name, $table, $timestamps);
            }
        } catch (QueryException $exception) {
            $this->error("Error: " . $exception->getMessage());
        }
    }
}
