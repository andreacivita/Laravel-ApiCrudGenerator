<?php

namespace AndreaCivita\ApiCrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
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
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD operations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
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
     * @param $type
     * @return bool|string
     */
    protected function getStub($type)
    {
        return file_get_contents(resource_path("stubs/$type.stub"));
    }


    /**
     * Generate model class from stubs
     * @param $name string name of model class
     * @param $table string name of DB table
     * @param $timestamps boolean set timestamps true | false
     */
    protected function model($name, $table, $timestamps)
    {
        $table === "default" ? $table = strtolower(str_plural($name)) : null;
        $timeDeclaration = 'public $timestamps = false;';
        if ($timestamps == "true")
            $timeDeclaration = 'public $timestamps = true;';
        $modelTemplate = str_replace(
            [
                '{{modelName}}',
                '{{tableDeclaration}}',
                '{{timestamps}}'
            ],
            [
                $name,
                $table,
                $timeDeclaration,
            ],
            $this->getStub('Model')
        );

        if (!file_exists($path = app_path('/Models')))
            mkdir($path, 0777, true);


        file_put_contents(app_path("Models/{$name}.php"), $modelTemplate);
    }

    /**
     * Create controller from controller.stub
     * @param $name
     */
    protected function controller($name)
    {
        $controllerTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}'
            ],
            [
                $name,
                strtolower(str_plural($name)),
                strtolower($name)
            ],
            $this->getStub('Controller')
        );

        file_put_contents(app_path("/Http/Controllers/{$name}Controller.php"), $controllerTemplate);
    }

    /**
     * Generate Request from request.stub
     * @param $name
     */
    protected function request($name)
    {
        $requestTemplate = str_replace(
            ['{{modelName}}'],
            [$name],
            $this->getStub('Request')
        );

        if (!file_exists($path = app_path('/Http/Requests')))
            mkdir($path, 0777, true);

        file_put_contents(app_path("/Http/Requests/{$name}Request.php"), $requestTemplate);
    }

    /**
     * Generate Resource from Resource.stub
     * @param $name
     */
    protected function resource($name)
    {
        $requestTemplate = str_replace(
            ['{{modelName}}'],
            [$name],
            $this->getStub('Resource')
        );

        if (!file_exists($path = app_path('/Http/Resources')))
            mkdir($path, 0777, true);

        file_put_contents(app_path("/Http/Resources/{$name}Resource.php"), $requestTemplate);
    }

    /**
     * Generate routes
     * @param $name
     */
    protected function routes($name, $table)
    {
        $table === "default" ? $table = strtolower(str_plural($name)) : null;
        $requestTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}'
            ],
            [
                $name,
                $table,
                strtolower($name)
            ],
            $this->getStub('Routes')
        );
        File::append(base_path('routes/api.php'), $requestTemplate);
    }

    /**
     * Generate unit test
     * @param $name
     * @param $table
     */
    protected function test($name, $table)
    {

        $testTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
            ],
            [
                $name,
                $table,
                strtolower($name)
            ],
            $this->getStub('Test')
        );
        File::append(base_path("tests/Unit/{$name}Test.php"), $testTemplate);
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
        $this->info("Generated Request!");
        $this->routes($name, $table);
        $this->info("Generated routes!");
        $this->test($name, $table);
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
